package com.example.maretlagadiwelfarecentre

import android.Manifest
import android.content.Intent
import android.content.pm.PackageManager
import android.os.Bundle
import android.speech.RecognizerIntent
import android.speech.SpeechRecognizer
import androidx.activity.ComponentActivity
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.compose.setContent
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.animation.core.animateFloatAsState
import androidx.compose.animation.core.tween
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.foundation.verticalScroll
import androidx.compose.foundation.rememberScrollState
import androidx.compose.ui.draw.alpha
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

/*
 * ============================================================
 * NOTE ON DATA / PERSISTENCE
 * ============================================================
 * This app has no backend. ChatRepository below is an in-memory
 * store (mutableStateListOf) so the Admin Dashboard can show
 * "who sent what, and when" for the current app session. Data
 * resets when the app process dies. To persist this across
 * restarts/devices, swap ChatRepository's storage for Room
 * (local) or Firebase Firestore (multi-device, real admin use).
 * The rest of the app is written so that swap only touches this
 * one object.
 * ============================================================
 */

// ============================================================
// THEME — colors, typography, shapes
// ============================================================

val Green = Color(0xFF1B7A3D)
val GreenDark = Color(0xFF0D3B12)
val GreenLight = Color(0xFFE8F5E9)
val Accent = Color(0xFFF2A93B)
val Red = Color(0xFFC62828)
val Background = Color(0xFFF7FAF7)
val SurfaceCard = Color(0xFFFFFFFF)
val TextMuted = Color(0xFF6B7A6E)

private val AppColorScheme = lightColorScheme(
    primary = Green,
    onPrimary = Color.White,
    primaryContainer = GreenLight,
    onPrimaryContainer = GreenDark,
    secondary = Accent,
    onSecondary = Color.White,
    background = Background,
    onBackground = Color(0xFF1B231C),
    surface = SurfaceCard,
    onSurface = Color(0xFF1B231C),
    error = Red,
    onError = Color.White
)

private val AppTypography = Typography(
    headlineMedium = Typography().headlineMedium.copy(fontWeight = FontWeight.Bold),
    titleLarge = Typography().titleLarge.copy(fontWeight = FontWeight.Bold),
    titleMedium = Typography().titleMedium.copy(fontWeight = FontWeight.SemiBold),
    bodyMedium = Typography().bodyMedium.copy(color = Color(0xFF3A453C))
)

val CardShape = RoundedCornerShape(20.dp)
val FieldShape = RoundedCornerShape(14.dp)
val ButtonShape = RoundedCornerShape(16.dp)

@Composable
fun MaretlagadiTheme(content: @Composable () -> Unit) {
    MaterialTheme(
        colorScheme = AppColorScheme,
        typography = AppTypography,
        content = content
    )
}

fun formatTimestamp(millis: Long): String {
    val fmt = SimpleDateFormat("dd MMM yyyy, HH:mm:ss", Locale.getDefault())
    return fmt.format(Date(millis))
}

// ============================================================
// DATA MODELS
// ============================================================

data class AppUser(
    val name: String,
    val email: String,
    val isAdmin: Boolean,
    val registeredAt: Long = System.currentTimeMillis()
)

data class ChatLogEntry(
    val id: Long,
    val userName: String,
    val userEmail: String,
    val message: String,
    val isUser: Boolean,
    val timestamp: Long
)

// Kept for the chat screen's own scroll list (per-conversation view)
data class ChatMessage(
    val message: String,
    val isUser: Boolean,
    val timestamp: Long = System.currentTimeMillis()
)

// ============================================================
// IN-MEMORY REPOSITORY (admin reads from here)
// ============================================================

object ChatRepository {

    private var nextId = 0L

    val allMessages = mutableStateListOf<ChatLogEntry>()

    val allUsers = mutableStateListOf<AppUser>()

    fun logMessage(user: AppUser, message: String, isUser: Boolean) {
        allMessages.add(
            ChatLogEntry(
                id = nextId++,
                userName = user.name,
                userEmail = user.email,
                message = message,
                isUser = isUser,
                timestamp = System.currentTimeMillis()
            )
        )
    }

    fun registerOrTrackUser(user: AppUser) {
        if (allUsers.none { it.email.equals(user.email, ignoreCase = true) }) {
            allUsers.add(user)
        }
    }
}

// admin accounts are recognised by email — swap this for a real role check
// against your backend once you have one.
private val ADMIN_EMAILS = setOf("admin@maretlagadi.org")

fun buildAppUser(email: String, name: String): AppUser {
    val isAdmin = ADMIN_EMAILS.any { it.equals(email.trim(), ignoreCase = true) }
    return AppUser(name = name, email = email.trim(), isAdmin = isAdmin)
}


// ============================================================
// MAIN ACTIVITY
// ============================================================

class MainActivity : ComponentActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        setContent {
            MaretlagadiTheme {
                MaretlagadiApp()
            }
        }
    }
}


// ============================================================
// MAIN APP — navigation state machine
// ============================================================

@Composable
fun MaretlagadiApp() {

    var currentScreen by remember { mutableStateOf("splash") }
    var currentUser by remember { mutableStateOf<AppUser?>(null) }

    when (currentScreen) {

        "splash" -> {
            SplashScreen { currentScreen = "login" }
        }

        "login" -> {
            LoginScreen(
                onLoginSuccess = { user ->
                    currentUser = user
                    ChatRepository.registerOrTrackUser(user)
                    currentScreen = "home"
                },
                onRegisterClick = { currentScreen = "register" },
                onForgotPasswordClick = { currentScreen = "forgot" }
            )
        }

        "register" -> {
            RegisterScreen(
                onRegisterSuccess = { user ->
                    currentUser = user
                    ChatRepository.registerOrTrackUser(user)
                    currentScreen = "home"
                },
                onBackToLogin = { currentScreen = "login" }
            )
        }

        "forgot" -> {
            ForgotPasswordScreen { currentScreen = "login" }
        }

        "home", "programmes", "events", "volunteer", "notifications",
        "donations", "contact", "settings", "profile", "admin" -> {

            val user = currentUser ?: AppUser("Guest", "guest@local", false)

            MainApplication(
                currentScreen = currentScreen,
                currentUser = user,
                onNavigate = { currentScreen = it },
                onLogout = {
                    currentUser = null
                    currentScreen = "login"
                }
            )
        }
    }
}


// ============================================================
// SPLASH SCREEN
// ============================================================

@Composable
fun SplashScreen(onFinished: () -> Unit) {

    var visible by remember { mutableStateOf(false) }
    val alpha by animateFloatAsState(
        targetValue = if (visible) 1f else 0f,
        animationSpec = tween(700), label = "splashAlpha"
    )

    LaunchedEffect(Unit) {
        visible = true
        kotlinx.coroutines.delay(2000)
        onFinished()
    }

    Surface(
        modifier = Modifier.fillMaxSize(),
        color = Green
    ) {
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(
                    Brush.verticalGradient(listOf(Green, GreenDark))
                )
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(24.dp)
                    .alpha(alpha),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.Center
            ) {
                Box(
                    modifier = Modifier
                        .size(120.dp)
                        .clip(RoundedCornerShape(32.dp))
                        .background(Color.White),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        text = "MWC",
                        fontSize = 32.sp,
                        fontWeight = FontWeight.Bold,
                        color = Green
                    )
                }

                Spacer(modifier = Modifier.height(24.dp))

                Text(
                    text = "Maretlagadi",
                    fontSize = 30.sp,
                    fontWeight = FontWeight.Bold,
                    color = Color.White
                )
                Text(
                    text = "Welfare Centre",
                    fontSize = 18.sp,
                    color = Color.White.copy(alpha = 0.9f)
                )

                Spacer(modifier = Modifier.height(36.dp))

                CircularProgressIndicator(color = Color.White, strokeWidth = 3.dp)
            }
        }
    }
}

// ============================================================
// LOGIN SCREEN
// ============================================================

@Composable
fun LoginScreen(
    onLoginSuccess: (AppUser) -> Unit,
    onRegisterClick: () -> Unit,
    onForgotPasswordClick: () -> Unit
) {
    var email by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var passwordVisible by remember { mutableStateOf(false) }
    var errorMessage by remember { mutableStateOf("") }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Background)
    ) {
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(24.dp)
                .verticalScroll(rememberScrollState()),
            verticalArrangement = Arrangement.Center
        ) {

            Box(
                modifier = Modifier
                    .size(64.dp)
                    .clip(RoundedCornerShape(18.dp))
                    .background(GreenLight),
                contentAlignment = Alignment.Center
            ) {
                Text("MWC", color = Green, fontWeight = FontWeight.Bold, fontSize = 18.sp)
            }

            Spacer(modifier = Modifier.height(20.dp))

            Text("Welcome back", style = MaterialTheme.typography.headlineMedium)
            Spacer(modifier = Modifier.height(6.dp))
            Text(
                "Login to continue to Maretlagadi Welfare Centre",
                color = TextMuted
            )

            Spacer(modifier = Modifier.height(32.dp))

            ElevatedCard(shape = CardShape) {
                Column(modifier = Modifier.padding(20.dp)) {

                    OutlinedTextField(
                        value = email,
                        onValueChange = { email = it; errorMessage = "" },
                        label = { Text("Email Address") },
                        leadingIcon = { Icon(Icons.Default.Email, contentDescription = "Email") },
                        shape = FieldShape,
                        modifier = Modifier.fillMaxWidth(),
                        singleLine = true
                    )

                    Spacer(modifier = Modifier.height(14.dp))

                    OutlinedTextField(
                        value = password,
                        onValueChange = { password = it; errorMessage = "" },
                        label = { Text("Password") },
                        leadingIcon = { Icon(Icons.Default.Lock, contentDescription = "Password") },
                        trailingIcon = {
                            IconButton(onClick = { passwordVisible = !passwordVisible }) {
                                Icon(
                                    imageVector = if (passwordVisible) Icons.Default.VisibilityOff else Icons.Default.Visibility,
                                    contentDescription = "Show Password"
                                )
                            }
                        },
                        visualTransformation = if (passwordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                        shape = FieldShape,
                        modifier = Modifier.fillMaxWidth(),
                        singleLine = true
                    )

                    Spacer(modifier = Modifier.height(8.dp))

                    Text(
                        text = "Forgot Password?",
                        color = Green,
                        fontWeight = FontWeight.Bold,
                        modifier = Modifier
                            .align(Alignment.End)
                            .clickable { onForgotPasswordClick() }
                    )

                    if (errorMessage.isNotEmpty()) {
                        Spacer(modifier = Modifier.height(10.dp))
                        AssistiveMessage(errorMessage, isError = true)
                    }

                    Spacer(modifier = Modifier.height(18.dp))

                    Button(
                        onClick = {
                            when {
                                email.isBlank() -> errorMessage = "Please enter your email."
                                !email.contains("@") -> errorMessage = "Please enter a valid email."
                                password.length < 6 -> errorMessage = "Password must be at least 6 characters."
                                else -> {
                                    val name = email.substringBefore("@")
                                        .replaceFirstChar { it.uppercase() }
                                    onLoginSuccess(buildAppUser(email, name))
                                }
                            }
                        },
                        modifier = Modifier.fillMaxWidth().height(54.dp),
                        shape = ButtonShape,
                        colors = ButtonDefaults.buttonColors(containerColor = Green)
                    ) {
                        Text("Login", fontSize = 16.sp, fontWeight = FontWeight.SemiBold)
                    }

                    Spacer(modifier = Modifier.height(8.dp))
                    Text(
                        "Admin? Sign in with admin@maretlagadi.org",
                        fontSize = 12.sp,
                        color = TextMuted,
                        textAlign = TextAlign.Center,
                        modifier = Modifier.fillMaxWidth()
                    )
                }
            }

            Spacer(modifier = Modifier.height(20.dp))

            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.Center
            ) {
                Text("Don't have an account? ", color = TextMuted)
                Text(
                    text = "Register",
                    color = Green,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.clickable { onRegisterClick() }
                )
            }
        }
    }
}

@Composable
fun AssistiveMessage(text: String, isError: Boolean) {
    Surface(
        color = if (isError) Red.copy(alpha = 0.08f) else Green.copy(alpha = 0.08f),
        shape = RoundedCornerShape(10.dp)
    ) {
        Text(
            text = text,
            color = if (isError) Red else Green,
            modifier = Modifier.padding(horizontal = 12.dp, vertical = 8.dp),
            fontSize = 13.sp
        )
    }
}

// ============================================================
// REGISTER SCREEN
// ============================================================

@Composable
fun RegisterScreen(
    onRegisterSuccess: (AppUser) -> Unit,
    onBackToLogin: () -> Unit
) {
    var name by remember { mutableStateOf("") }
    var email by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var confirmPassword by remember { mutableStateOf("") }
    var errorMessage by remember { mutableStateOf("") }

    LazyColumn(
        modifier = Modifier
            .fillMaxSize()
            .background(Background)
            .padding(24.dp),
        verticalArrangement = Arrangement.Center
    ) {
        item {
            Text("Create Account", style = MaterialTheme.typography.headlineMedium)
            Spacer(modifier = Modifier.height(6.dp))
            Text("Join the Maretlagadi Welfare Centre community", color = TextMuted)

            Spacer(modifier = Modifier.height(24.dp))

            ElevatedCard(shape = CardShape) {
                Column(modifier = Modifier.padding(20.dp)) {

                    OutlinedTextField(
                        value = name,
                        onValueChange = { name = it; errorMessage = "" },
                        label = { Text("Full Name") },
                        leadingIcon = { Icon(Icons.Default.Person, contentDescription = null) },
                        shape = FieldShape,
                        modifier = Modifier.fillMaxWidth()
                    )

                    Spacer(modifier = Modifier.height(14.dp))

                    OutlinedTextField(
                        value = email,
                        onValueChange = { email = it; errorMessage = "" },
                        label = { Text("Email Address") },
                        leadingIcon = { Icon(Icons.Default.Email, contentDescription = null) },
                        shape = FieldShape,
                        modifier = Modifier.fillMaxWidth()
                    )

                    Spacer(modifier = Modifier.height(14.dp))

                    OutlinedTextField(
                        value = password,
                        onValueChange = { password = it },
                        label = { Text("Password") },
                        leadingIcon = { Icon(Icons.Default.Lock, contentDescription = null) },
                        visualTransformation = PasswordVisualTransformation(),
                        shape = FieldShape,
                        modifier = Modifier.fillMaxWidth()
                    )

                    Spacer(modifier = Modifier.height(14.dp))

                    OutlinedTextField(
                        value = confirmPassword,
                        onValueChange = { confirmPassword = it },
                        label = { Text("Confirm Password") },
                        leadingIcon = { Icon(Icons.Default.Lock, contentDescription = null) },
                        visualTransformation = PasswordVisualTransformation(),
                        shape = FieldShape,
                        modifier = Modifier.fillMaxWidth()
                    )

                    if (errorMessage.isNotEmpty()) {
                        Spacer(modifier = Modifier.height(14.dp))
                        AssistiveMessage(errorMessage, isError = true)
                    }

                    Spacer(modifier = Modifier.height(18.dp))

                    Button(
                        onClick = {
                            when {
                                name.isBlank() -> errorMessage = "Please enter your name."
                                !email.contains("@") -> errorMessage = "Enter a valid email."
                                password.length < 6 -> errorMessage = "Password must be at least 6 characters."
                                password != confirmPassword -> errorMessage = "Passwords do not match."
                                else -> onRegisterSuccess(buildAppUser(email, name))
                            }
                        },
                        modifier = Modifier.fillMaxWidth().height(54.dp),
                        shape = ButtonShape,
                        colors = ButtonDefaults.buttonColors(containerColor = Green)
                    ) {
                        Text("Create Account", fontWeight = FontWeight.SemiBold)
                    }
                }
            }

            Spacer(modifier = Modifier.height(20.dp))

            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.Center
            ) {
                Text("Already have an account? ", color = TextMuted)
                Text(
                    text = "Login",
                    color = Green,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.clickable { onBackToLogin() }
                )
            }
        }
    }
}


// ============================================================
// FORGOT PASSWORD
// ============================================================

@Composable
fun ForgotPasswordScreen(onBackToLogin: () -> Unit) {

    var email by remember { mutableStateOf("") }
    var message by remember { mutableStateOf("") }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Background)
            .padding(24.dp),
        verticalArrangement = Arrangement.Center
    ) {
        Text("Forgot Password?", style = MaterialTheme.typography.headlineMedium)
        Spacer(modifier = Modifier.height(8.dp))
        Text("Enter your email address to reset your password.", color = TextMuted)

        Spacer(modifier = Modifier.height(24.dp))

        ElevatedCard(shape = CardShape) {
            Column(modifier = Modifier.padding(20.dp)) {

                OutlinedTextField(
                    value = email,
                    onValueChange = { email = it; message = "" },
                    label = { Text("Email Address") },
                    shape = FieldShape,
                    modifier = Modifier.fillMaxWidth()
                )

                Spacer(modifier = Modifier.height(16.dp))

                Button(
                    onClick = {
                        message = if (email.contains("@"))
                            "Password reset instructions sent successfully!"
                        else
                            "Please enter a valid email address."
                    },
                    modifier = Modifier.fillMaxWidth().height(54.dp),
                    shape = ButtonShape,
                    colors = ButtonDefaults.buttonColors(containerColor = Green)
                ) {
                    Text("Send Instructions", fontWeight = FontWeight.SemiBold)
                }

                if (message.isNotEmpty()) {
                    Spacer(modifier = Modifier.height(14.dp))
                    AssistiveMessage(message, isError = !message.contains("successfully"))
                }
            }
        }

        Spacer(modifier = Modifier.height(24.dp))

        Text(
            text = "Back to Login",
            color = Green,
            fontWeight = FontWeight.Bold,
            modifier = Modifier
                .align(Alignment.CenterHorizontally)
                .clickable { onBackToLogin() }
        )
    }
}


// ============================================================
// MAIN APPLICATION SHELL
// ============================================================

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun MainApplication(
    currentScreen: String,
    currentUser: AppUser,
    onNavigate: (String) -> Unit,
    onLogout: () -> Unit
) {
    var showChatbot by remember { mutableStateOf(false) }

    val titles = mapOf(
        "home" to "Home",
        "programmes" to "Programmes",
        "events" to "Events",
        "volunteer" to "Volunteer",
        "notifications" to "Notifications",
        "donations" to "Donations",
        "contact" to "Contact Us",
        "settings" to "Settings",
        "profile" to "Profile",
        "admin" to "Admin Dashboard"
    )

    Scaffold(
        containerColor = Background,
        topBar = {
            TopAppBar(
                title = { Text(titles[currentScreen] ?: "Maretlagadi") },
                navigationIcon = {
                    if (currentScreen != "home") {
                        IconButton(onClick = { onNavigate("home") }) {
                            Icon(Icons.Default.ArrowBack, contentDescription = "Back")
                        }
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = Background,
                    titleContentColor = Color(0xFF1B231C)
                )
            )
        },
        floatingActionButton = {
            if (currentScreen != "admin") {
                ExtendedFloatingActionButton(
                    onClick = { showChatbot = true },
                    containerColor = Green,
                    contentColor = Color.White,
                    icon = { Icon(Icons.Default.SmartToy, contentDescription = "Chatbot") },
                    text = { Text("Ask us") }
                )
            }
        },
        bottomBar = {
            BottomNavigationBar(currentScreen = currentScreen, onNavigate = onNavigate)
        }
    ) { paddingValues ->

        Box(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
        ) {
            when (currentScreen) {
                "home" -> HomeScreen(userName = currentUser.name, onNavigate = onNavigate)
                "programmes" -> ProgrammesScreen()
                "events" -> EventsScreen()
                "volunteer" -> VolunteerScreen()
                "notifications" -> NotificationsScreen()
                "donations" -> DonationsScreen()
                "contact" -> ContactScreen()
                "settings" -> SettingsScreen()
                "profile" -> ProfileScreen(
                    currentUser = currentUser,
                    onLogout = onLogout,
                    onOpenAdmin = { onNavigate("admin") }
                )
                "admin" -> AdminDashboardScreen(currentUser = currentUser)
            }
        }
    }

    if (showChatbot) {
        ChatbotScreen(
            currentUser = currentUser,
            onClose = { showChatbot = false }
        )
    }
}


// ============================================================
// BOTTOM NAVIGATION
// ============================================================

@Composable
fun BottomNavigationBar(currentScreen: String, onNavigate: (String) -> Unit) {

    NavigationBar(containerColor = Color.White) {

        NavigationBarItem(
            selected = currentScreen == "home",
            onClick = { onNavigate("home") },
            icon = { Icon(Icons.Default.Home, contentDescription = "Home") },
            label = { Text("Home") },
            colors = NavigationBarItemDefaults.colors(selectedIconColor = Green, indicatorColor = GreenLight)
        )

        NavigationBarItem(
            selected = currentScreen == "programmes",
            onClick = { onNavigate("programmes") },
            icon = { Icon(Icons.Default.MenuBook, contentDescription = "Programmes") },
            label = { Text("Programmes") },
            colors = NavigationBarItemDefaults.colors(selectedIconColor = Green, indicatorColor = GreenLight)
        )

        NavigationBarItem(
            selected = currentScreen == "events",
            onClick = { onNavigate("events") },
            icon = { Icon(Icons.Default.Event, contentDescription = "Events") },
            label = { Text("Events") },
            colors = NavigationBarItemDefaults.colors(selectedIconColor = Green, indicatorColor = GreenLight)
        )

        NavigationBarItem(
            selected = currentScreen == "volunteer",
            onClick = { onNavigate("volunteer") },
            icon = { Icon(Icons.Default.VolunteerActivism, contentDescription = "Volunteer") },
            label = { Text("Volunteer") },
            colors = NavigationBarItemDefaults.colors(selectedIconColor = Green, indicatorColor = GreenLight)
        )
    }
}


// ============================================================
// HOME SCREEN
// ============================================================

@Composable
fun HomeScreen(userName: String, onNavigate: (String) -> Unit) {

    LazyColumn(
        modifier = Modifier.fillMaxSize().padding(20.dp)
    ) {
        item {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Column(modifier = Modifier.weight(1f)) {
                    Text("Hello, $userName 👋", fontSize = 24.sp, fontWeight = FontWeight.Bold)
                    Text("Welcome to Maretlagadi Welfare Centre", color = TextMuted)
                }

                IconButton(
                    onClick = { onNavigate("profile") },
                    modifier = Modifier.size(50.dp).clip(CircleShape).background(GreenLight)
                ) {
                    Icon(Icons.Default.Person, contentDescription = "Profile", tint = Green)
                }
            }

            Spacer(modifier = Modifier.height(22.dp))

            HeroCard()

            Spacer(modifier = Modifier.height(26.dp))

            Text("Quick Actions", fontSize = 20.sp, fontWeight = FontWeight.Bold)
            Spacer(modifier = Modifier.height(14.dp))

            LazyRow(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                item { ActionCard("Programmes", Icons.Default.MenuBook) { onNavigate("programmes") } }
                item { ActionCard("Events", Icons.Default.Event) { onNavigate("events") } }
                item { ActionCard("Volunteer", Icons.Default.VolunteerActivism) { onNavigate("volunteer") } }
                item { ActionCard("Donate", Icons.Default.Favorite) { onNavigate("donations") } }
            }

            Spacer(modifier = Modifier.height(26.dp))

            Text("Latest Updates", fontSize = 20.sp, fontWeight = FontWeight.Bold)
            Spacer(modifier = Modifier.height(12.dp))

            UpdateCard("Community Programme", "Discover programmes and services available to the community.")
            Spacer(modifier = Modifier.height(12.dp))
            UpdateCard("Upcoming Community Event", "Join us and make a positive difference in our community.")

            Spacer(modifier = Modifier.height(90.dp))
        }
    }
}

@Composable
fun HeroCard() {
    Card(
        modifier = Modifier.fillMaxWidth().height(180.dp),
        shape = CardShape,
        colors = CardDefaults.cardColors(containerColor = Green)
    ) {
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(Brush.horizontalGradient(listOf(Green, GreenDark)))
        ) {
            Column(
                modifier = Modifier.fillMaxSize().padding(22.dp),
                verticalArrangement = Arrangement.Center
            ) {
                Text("Together we can", fontSize = 24.sp, fontWeight = FontWeight.Bold, color = Color.White)
                Text("make a difference ❤️", fontSize = 24.sp, fontWeight = FontWeight.Bold, color = Color.White)
                Spacer(modifier = Modifier.height(10.dp))
                Text("Supporting and empowering our community.", color = Color.White.copy(alpha = 0.9f))
            }
        }
    }
}

@Composable
fun ActionCard(title: String, icon: androidx.compose.ui.graphics.vector.ImageVector, onClick: () -> Unit) {
    ElevatedCard(
        modifier = Modifier.width(120.dp).height(124.dp).clickable { onClick() },
        shape = RoundedCornerShape(18.dp),
        colors = CardDefaults.elevatedCardColors(containerColor = GreenLight)
    ) {
        Column(
            modifier = Modifier.fillMaxSize().padding(14.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            Icon(imageVector = icon, contentDescription = title, tint = Green, modifier = Modifier.size(34.dp))
            Spacer(modifier = Modifier.height(10.dp))
            Text(title, fontWeight = FontWeight.SemiBold, fontSize = 13.sp, textAlign = TextAlign.Center)
        }
    }
}

@Composable
fun UpdateCard(title: String, description: String) {
    ElevatedCard(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(16.dp)) {
        Column(modifier = Modifier.padding(16.dp)) {
            Text(title, fontSize = 16.sp, fontWeight = FontWeight.Bold)
            Spacer(modifier = Modifier.height(6.dp))
            Text(description, color = TextMuted, fontSize = 13.sp)
        }
    }
}


// ============================================================
// PROGRAMMES / EVENTS / NOTIFICATIONS (shared list screen)
// ============================================================

@Composable
fun ProgrammesScreen() {
    val programmes = listOf(
        "Youth Development" to "Supporting young people with skills and opportunities.",
        "Community Support" to "Providing support and assistance to vulnerable communities.",
        "Education Programme" to "Helping learners and community members access education.",
        "Skills Development" to "Empowering people with practical and professional skills."
    )
    ScreenList(title = "Programmes", icon = Icons.Default.MenuBook, items = programmes)
}

@Composable
fun EventsScreen() {
    val events = listOf(
        "Community Outreach" to "Join our community outreach programme.",
        "Youth Workshop" to "A workshop focused on youth empowerment.",
        "Fundraising Event" to "Help us raise support for the welfare centre.",
        "Community Meeting" to "Discuss important community initiatives."
    )
    ScreenList(title = "Upcoming Events", icon = Icons.Default.Event, items = events)
}

@Composable
fun NotificationsScreen() {
    val notifications = listOf(
        "New Community Event" to "A new community event has been added.",
        "Volunteer Update" to "Your volunteer application is being reviewed.",
        "Programme Update" to "A new programme is now available."
    )
    ScreenList(title = "Notifications", icon = Icons.Default.Notifications, items = notifications)
}

@Composable
fun ScreenList(title: String, icon: androidx.compose.ui.graphics.vector.ImageVector, items: List<Pair<String, String>>) {

    LazyColumn(modifier = Modifier.fillMaxSize().padding(20.dp)) {

        item {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Box(
                    modifier = Modifier.size(44.dp).clip(RoundedCornerShape(12.dp)).background(GreenLight),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(icon, contentDescription = null, tint = Green, modifier = Modifier.size(24.dp))
                }
                Spacer(modifier = Modifier.width(12.dp))
                Text(title, fontSize = 22.sp, fontWeight = FontWeight.Bold)
            }
            Spacer(modifier = Modifier.height(20.dp))
        }

        items(items) { item ->
            ElevatedCard(
                modifier = Modifier.fillMaxWidth().padding(bottom = 12.dp),
                shape = RoundedCornerShape(16.dp)
            ) {
                Column(modifier = Modifier.padding(16.dp)) {
                    Text(item.first, fontSize = 16.sp, fontWeight = FontWeight.Bold)
                    Spacer(modifier = Modifier.height(6.dp))
                    Text(item.second, color = TextMuted, fontSize = 13.sp)
                }
            }
        }

        item { Spacer(modifier = Modifier.height(90.dp)) }
    }
}


// ============================================================
// VOLUNTEER SCREEN
// ============================================================

@Composable
fun VolunteerScreen() {

    var name by remember { mutableStateOf("") }
    var email by remember { mutableStateOf("") }
    var message by remember { mutableStateOf("") }

    LazyColumn(modifier = Modifier.fillMaxSize().padding(20.dp)) {
        item {
            Text("Become a Volunteer", fontSize = 24.sp, fontWeight = FontWeight.Bold)
            Spacer(modifier = Modifier.height(6.dp))
            Text("Join us and help make a difference in your community.", color = TextMuted)

            Spacer(modifier = Modifier.height(22.dp))

            ElevatedCard(shape = CardShape) {
                Column(modifier = Modifier.padding(20.dp)) {

                    OutlinedTextField(
                        value = name,
                        onValueChange = { name = it },
                        label = { Text("Full Name") },
                        shape = FieldShape,
                        modifier = Modifier.fillMaxWidth()
                    )

                    Spacer(modifier = Modifier.height(14.dp))

                    OutlinedTextField(
                        value = email,
                        onValueChange = { email = it },
                        label = { Text("Email Address") },
                        shape = FieldShape,
                        modifier = Modifier.fillMaxWidth()
                    )

                    Spacer(modifier = Modifier.height(18.dp))

                    Button(
                        onClick = {
                            message = if (name.isNotBlank() && email.contains("@"))
                                "Thank you! Your volunteer application has been submitted."
                            else
                                "Please enter your name and valid email."
                        },
                        modifier = Modifier.fillMaxWidth().height(54.dp),
                        shape = ButtonShape,
                        colors = ButtonDefaults.buttonColors(containerColor = Green)
                    ) {
                        Text("Apply to Volunteer", fontWeight = FontWeight.SemiBold)
                    }

                    if (message.isNotEmpty()) {
                        Spacer(modifier = Modifier.height(14.dp))
                        AssistiveMessage(message, isError = !message.contains("Thank"))
                    }
                }
            }
        }
    }
}


// ============================================================
// DONATIONS
// ============================================================

@Composable
fun DonationsScreen() {
    Column(modifier = Modifier.fillMaxSize().padding(20.dp)) {
        Text("Support Our Community ❤️", fontSize = 22.sp, fontWeight = FontWeight.Bold)
        Spacer(modifier = Modifier.height(8.dp))
        Text("Your contribution helps us support and empower the community.", color = TextMuted)

        Spacer(modifier = Modifier.height(26.dp))

        DonationButton("R50")
        Spacer(modifier = Modifier.height(12.dp))
        DonationButton("R100")
        Spacer(modifier = Modifier.height(12.dp))
        DonationButton("R250")
        Spacer(modifier = Modifier.height(12.dp))
        DonationButton("Custom Donation")
    }
}

@Composable
fun DonationButton(amount: String) {
    Button(
        onClick = { },
        modifier = Modifier.fillMaxWidth().height(56.dp),
        shape = ButtonShape,
        colors = ButtonDefaults.buttonColors(containerColor = Green)
    ) {
        Text("Donate $amount", fontWeight = FontWeight.SemiBold)
    }
}


// ============================================================
// CONTACT
// ============================================================

@Composable
fun ContactScreen() {
    Column(modifier = Modifier.fillMaxSize().padding(20.dp)) {
        Text("Contact Us", fontSize = 24.sp, fontWeight = FontWeight.Bold)
        Spacer(modifier = Modifier.height(20.dp))

        ContactItem(Icons.Default.Email, "Email", "info@maretlagadi.org")
        ContactItem(Icons.Default.Phone, "Phone", "+27 XX XXX XXXX")
        ContactItem(Icons.Default.LocationOn, "Location", "Maretlagadi Welfare Centre")
    }
}

@Composable
fun ContactItem(icon: androidx.compose.ui.graphics.vector.ImageVector, title: String, value: String) {
    ElevatedCard(modifier = Modifier.fillMaxWidth().padding(bottom = 12.dp), shape = RoundedCornerShape(16.dp)) {
        Row(modifier = Modifier.padding(16.dp), verticalAlignment = Alignment.CenterVertically) {
            Box(
                modifier = Modifier.size(40.dp).clip(RoundedCornerShape(10.dp)).background(GreenLight),
                contentAlignment = Alignment.Center
            ) {
                Icon(icon, contentDescription = null, tint = Green, modifier = Modifier.size(20.dp))
            }
            Spacer(modifier = Modifier.width(16.dp))
            Column {
                Text(title, fontWeight = FontWeight.Bold)
                Text(value, color = TextMuted)
            }
        }
    }
}


// ============================================================
// SETTINGS
// ============================================================

@Composable
fun SettingsScreen() {
    var notifications by remember { mutableStateOf(true) }

    Column(modifier = Modifier.fillMaxSize().padding(20.dp)) {
        Text("Settings", fontSize = 24.sp, fontWeight = FontWeight.Bold)
        Spacer(modifier = Modifier.height(20.dp))

        ElevatedCard(shape = RoundedCornerShape(16.dp)) {
            Row(
                modifier = Modifier.fillMaxWidth().padding(16.dp),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Column {
                    Text("Notifications", fontWeight = FontWeight.Bold)
                    Text("Receive app notifications", color = TextMuted)
                }
                Switch(
                    checked = notifications,
                    onCheckedChange = { notifications = it },
                    colors = SwitchDefaults.colors(checkedTrackColor = Green)
                )
            }
        }
    }
}


// ============================================================
// PROFILE SCREEN
// ============================================================

@Composable
fun ProfileScreen(currentUser: AppUser, onLogout: () -> Unit, onOpenAdmin: () -> Unit) {

    Column(
        modifier = Modifier.fillMaxSize().padding(20.dp),
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        Spacer(modifier = Modifier.height(20.dp))

        Box(
            modifier = Modifier.size(110.dp).clip(CircleShape).background(GreenLight),
            contentAlignment = Alignment.Center
        ) {
            Icon(Icons.Default.Person, contentDescription = "Profile", tint = Green, modifier = Modifier.size(64.dp))
        }

        Spacer(modifier = Modifier.height(16.dp))

        Text(currentUser.name, fontSize = 22.sp, fontWeight = FontWeight.Bold)
        Text(currentUser.email, color = TextMuted, fontSize = 13.sp)

        if (currentUser.isAdmin) {
            Spacer(modifier = Modifier.height(8.dp))
            Surface(color = Accent.copy(alpha = 0.15f), shape = RoundedCornerShape(20.dp)) {
                Text(
                    "ADMIN",
                    color = Accent,
                    fontWeight = FontWeight.Bold,
                    fontSize = 11.sp,
                    modifier = Modifier.padding(horizontal = 12.dp, vertical = 4.dp)
                )
            }
        } else {
            Text("Maretlagadi Community Member", color = TextMuted, fontSize = 13.sp)
        }

        Spacer(modifier = Modifier.height(32.dp))

        if (currentUser.isAdmin) {
            ProfileOption(Icons.Default.AdminPanelSettings, "Admin Dashboard") { onOpenAdmin() }
        }
        ProfileOption(Icons.Default.Notifications, "Notifications")
        ProfileOption(Icons.Default.Settings, "Settings")
        ProfileOption(Icons.Default.ContactMail, "Contact Us")

        Spacer(modifier = Modifier.weight(1f))

        Button(
            onClick = onLogout,
            modifier = Modifier.fillMaxWidth().height(54.dp),
            shape = ButtonShape,
            colors = ButtonDefaults.buttonColors(containerColor = Red)
        ) {
            Icon(Icons.Default.Logout, contentDescription = null)
            Spacer(modifier = Modifier.width(8.dp))
            Text("Logout", fontWeight = FontWeight.SemiBold)
        }

        Spacer(modifier = Modifier.height(20.dp))
    }
}

@Composable
fun ProfileOption(
    icon: androidx.compose.ui.graphics.vector.ImageVector,
    title: String,
    onClick: (() -> Unit)? = null
) {
    ElevatedCard(
        modifier = Modifier
            .fillMaxWidth()
            .padding(bottom = 12.dp)
            .let { if (onClick != null) it.clickable { onClick() } else it },
        shape = RoundedCornerShape(14.dp)
    ) {
        Row(
            modifier = Modifier.fillMaxWidth().padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Icon(icon, contentDescription = null, tint = Green)
            Spacer(modifier = Modifier.width(16.dp))
            Text(title, fontWeight = FontWeight.Medium)
            Spacer(modifier = Modifier.weight(1f))
            Icon(Icons.Default.ChevronRight, contentDescription = null, tint = TextMuted)
        }
    }
}


// ============================================================
// ADMIN DASHBOARD — chat logs (who / what / when) + users
// ============================================================

@Composable
fun AdminDashboardScreen(currentUser: AppUser) {

    if (!currentUser.isAdmin) {
        Column(
            modifier = Modifier.fillMaxSize().padding(24.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            Icon(Icons.Default.Lock, contentDescription = null, tint = TextMuted, modifier = Modifier.size(48.dp))
            Spacer(modifier = Modifier.height(12.dp))
            Text("You need an admin account to view this page.", color = TextMuted, textAlign = TextAlign.Center)
        }
        return
    }

    var selectedTab by remember { mutableStateOf(0) }
    val tabs = listOf("Chat Logs", "Users")

    Column(modifier = Modifier.fillMaxSize()) {

        TabRow(
            selectedTabIndex = selectedTab,
            containerColor = Color.White,
            contentColor = Green
        ) {
            tabs.forEachIndexed { index, title ->
                Tab(
                    selected = selectedTab == index,
                    onClick = { selectedTab = index },
                    text = { Text(title) }
                )
            }
        }

        when (selectedTab) {
            0 -> AdminChatLogsTab()
            1 -> AdminUsersTab()
        }
    }
}

@Composable
fun AdminChatLogsTab() {

    val logs = ChatRepository.allMessages

    if (logs.isEmpty()) {
        EmptyState("No chatbot conversations yet.", Icons.Default.Chat)
        return
    }

    LazyColumn(modifier = Modifier.fillMaxSize().padding(16.dp)) {

        item {
            Surface(color = GreenLight, shape = RoundedCornerShape(12.dp)) {
                Text(
                    "${logs.size} message(s) across ${logs.map { it.userEmail }.distinct().size} user(s)",
                    modifier = Modifier.padding(12.dp),
                    color = GreenDark,
                    fontSize = 13.sp,
                    fontWeight = FontWeight.SemiBold
                )
            }
            Spacer(modifier = Modifier.height(14.dp))
        }

        // newest first
        items(logs.sortedByDescending { it.timestamp }) { entry ->
            ElevatedCard(
                modifier = Modifier.fillMaxWidth().padding(bottom = 10.dp),
                shape = RoundedCornerShape(14.dp)
            ) {
                Column(modifier = Modifier.padding(14.dp)) {

                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.SpaceBetween,
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Icon(
                                imageVector = if (entry.isUser) Icons.Default.Person else Icons.Default.SmartToy,
                                contentDescription = null,
                                tint = if (entry.isUser) Green else Accent,
                                modifier = Modifier.size(18.dp)
                            )
                            Spacer(modifier = Modifier.width(6.dp))
                            Text(
                                text = if (entry.isUser) entry.userName else "Assistant → ${entry.userName}",
                                fontWeight = FontWeight.Bold,
                                fontSize = 13.sp
                            )
                        }

                        Surface(
                            color = if (entry.isUser) Green.copy(alpha = 0.1f) else Accent.copy(alpha = 0.12f),
                            shape = RoundedCornerShape(8.dp)
                        ) {
                            Text(
                                text = if (entry.isUser) "USER" else "BOT",
                                fontSize = 10.sp,
                                fontWeight = FontWeight.Bold,
                                color = if (entry.isUser) Green else Accent,
                                modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp)
                            )
                        }
                    }

                    Spacer(modifier = Modifier.height(6.dp))
                    Text(entry.userEmail, fontSize = 11.sp, color = TextMuted)

                    Spacer(modifier = Modifier.height(8.dp))
                    Text(entry.message, fontSize = 14.sp)

                    Spacer(modifier = Modifier.height(8.dp))
                    Text(
                        formatTimestamp(entry.timestamp),
                        fontSize = 11.sp,
                        color = TextMuted
                    )
                }
            }
        }

        item { Spacer(modifier = Modifier.height(30.dp)) }
    }
}

@Composable
fun AdminUsersTab() {

    val users = ChatRepository.allUsers

    if (users.isEmpty()) {
        EmptyState("No users have logged in this session yet.", Icons.Default.People)
        return
    }

    LazyColumn(modifier = Modifier.fillMaxSize().padding(16.dp)) {

        items(users.sortedByDescending { it.registeredAt }) { user ->

            val messageCount = ChatRepository.allMessages.count {
                it.userEmail.equals(user.email, ignoreCase = true) && it.isUser
            }

            ElevatedCard(
                modifier = Modifier.fillMaxWidth().padding(bottom = 10.dp),
                shape = RoundedCornerShape(14.dp)
            ) {
                Row(
                    modifier = Modifier.fillMaxWidth().padding(14.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Box(
                        modifier = Modifier.size(42.dp).clip(CircleShape).background(GreenLight),
                        contentAlignment = Alignment.Center
                    ) {
                        Icon(Icons.Default.Person, contentDescription = null, tint = Green)
                    }
                    Spacer(modifier = Modifier.width(14.dp))
                    Column(modifier = Modifier.weight(1f)) {
                        Text(user.name, fontWeight = FontWeight.Bold, fontSize = 14.sp)
                        Text(user.email, color = TextMuted, fontSize = 12.sp)
                        Text(
                            "Last seen: ${formatTimestamp(user.registeredAt)} • $messageCount message(s)",
                            color = TextMuted,
                            fontSize = 11.sp
                        )
                    }
                    if (user.isAdmin) {
                        Surface(color = Accent.copy(alpha = 0.15f), shape = RoundedCornerShape(8.dp)) {
                            Text(
                                "ADMIN",
                                color = Accent,
                                fontWeight = FontWeight.Bold,
                                fontSize = 10.sp,
                                modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp)
                            )
                        }
                    }
                }
            }
        }

        item { Spacer(modifier = Modifier.height(30.dp)) }
    }
}

@Composable
fun EmptyState(text: String, icon: androidx.compose.ui.graphics.vector.ImageVector) {
    Column(
        modifier = Modifier.fillMaxSize().padding(24.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center
    ) {
        Icon(icon, contentDescription = null, tint = TextMuted, modifier = Modifier.size(44.dp))
        Spacer(modifier = Modifier.height(10.dp))
        Text(text, color = TextMuted, textAlign = TextAlign.Center)
    }
}


// ============================================================
// CHATBOT
// ============================================================

@Composable
fun ChatbotScreen(currentUser: AppUser, onClose: () -> Unit) {

    var userMessage by remember { mutableStateOf("") }
    var isListening by remember { mutableStateOf(false) }

    var messages by remember {
        mutableStateOf(
            listOf(
                ChatMessage("Hello! 👋 I am the Maretlagadi Assistant. How can I help you today?", false)
            )
        )
    }

    val context = LocalContext.current

    val microphonePermission = rememberLauncherForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { granted ->
        if (granted) {
            isListening = true
            startVoiceRecognition(context) { spokenText ->
                isListening = false
                if (spokenText.isNotBlank()) userMessage = spokenText
            }
        } else {
            isListening = false
        }
    }

    fun sendMessage() {
        if (userMessage.isNotBlank()) {
            val question = userMessage
            messages = messages + ChatMessage(question, true)
            ChatRepository.logMessage(currentUser, question, isUser = true)

            val answer = getChatbotResponse(question)
            messages = messages + ChatMessage(answer, false)
            ChatRepository.logMessage(currentUser, answer, isUser = false)

            userMessage = ""
        }
    }

    Surface(modifier = Modifier.fillMaxSize(), color = Background) {
        Column(modifier = Modifier.fillMaxSize()) {

            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(Brush.horizontalGradient(listOf(Green, GreenDark)))
                    .padding(16.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Icon(Icons.Default.SmartToy, contentDescription = null, tint = Color.White)
                Spacer(modifier = Modifier.width(12.dp))
                Column(modifier = Modifier.weight(1f)) {
                    Text("Maretlagadi Assistant", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 18.sp)
                    Text(
                        if (isListening) "🎤 Listening..." else "Online",
                        color = Color.White.copy(alpha = 0.9f),
                        fontSize = 12.sp
                    )
                }
                IconButton(onClick = onClose) {
                    Icon(Icons.Default.Close, contentDescription = "Close", tint = Color.White)
                }
            }

            LazyColumn(
                modifier = Modifier.weight(1f).padding(16.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                items(messages) { chat -> ChatBubble(chat) }
            }

            Row(
                modifier = Modifier.fillMaxWidth().padding(12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                OutlinedTextField(
                    value = userMessage,
                    onValueChange = { userMessage = it },
                    placeholder = { Text("Ask me something...") },
                    shape = FieldShape,
                    modifier = Modifier.weight(1f),
                    singleLine = true
                )

                Spacer(modifier = Modifier.width(4.dp))

                IconButton(
                    onClick = {
                        if (context.checkSelfPermission(Manifest.permission.RECORD_AUDIO) == PackageManager.PERMISSION_GRANTED) {
                            isListening = true
                            startVoiceRecognition(context) { spokenText ->
                                isListening = false
                                if (spokenText.isNotBlank()) userMessage = spokenText
                            }
                        } else {
                            microphonePermission.launch(Manifest.permission.RECORD_AUDIO)
                        }
                    }
                ) {
                    Icon(
                        Icons.Default.Mic,
                        contentDescription = "Voice Input",
                        tint = if (isListening) Red else Green
                    )
                }

                IconButton(onClick = { sendMessage() }) {
                    Icon(Icons.Default.Send, contentDescription = "Send", tint = Green)
                }
            }
        }
    }
}

@Composable
fun ChatBubble(chat: ChatMessage) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = if (chat.isUser) Arrangement.End else Arrangement.Start
    ) {
        Column(
            horizontalAlignment = if (chat.isUser) Alignment.End else Alignment.Start,
            modifier = Modifier.widthIn(max = 300.dp)
        ) {
            Card(
                colors = CardDefaults.cardColors(containerColor = if (chat.isUser) Green else Color.White),
                shape = RoundedCornerShape(
                    topStart = 16.dp, topEnd = 16.dp,
                    bottomStart = if (chat.isUser) 16.dp else 4.dp,
                    bottomEnd = if (chat.isUser) 4.dp else 16.dp
                ),
                elevation = CardDefaults.cardElevation(defaultElevation = 1.dp)
            ) {
                Text(
                    text = chat.message,
                    modifier = Modifier.padding(12.dp),
                    color = if (chat.isUser) Color.White else Color.Black,
                    fontSize = 14.sp
                )
            }
            Text(
                text = formatTimestamp(chat.timestamp).substringAfter(", "),
                fontSize = 10.sp,
                color = TextMuted,
                modifier = Modifier.padding(top = 3.dp, start = 4.dp, end = 4.dp)
            )
        }
    }
}


// ============================================================
// SMART CHATBOT RESPONSES
// ============================================================

fun getChatbotResponse(question: String): String {

    val message = question.lowercase()

    return when {
        message.contains("hello") || message.contains("hi") ->
            "Hello! 👋 How can I assist you today?"

        message.contains("volunteer") ->
            "You can become a volunteer by going to the Volunteer section and completing the application form."

        message.contains("programme") || message.contains("program") ->
            "You can explore our available community programmes in the Programmes section."

        message.contains("event") ->
            "You can view upcoming activities and community events in the Events section."

        message.contains("donate") || message.contains("donation") ->
            "Thank you for wanting to support us! ❤️ Please visit the Donations section."

        message.contains("register") || message.contains("sign up") ->
            "You can create an account from the Register screen."

        message.contains("password") ->
            "If you forgot your password, select Forgot Password on the Login screen."

        message.contains("contact") || message.contains("phone") || message.contains("email") ->
            "You can find our contact information in the Contact section."

        message.contains("notification") ->
            "You can view important updates in the Notifications section."

        else ->
            "I'm still learning 😊. You can ask me about volunteering, programmes, events, donations, registration, passwords or contact information."
    }
}


// ============================================================
// VOICE RECOGNITION
// ============================================================

fun startVoiceRecognition(context: android.content.Context, onResult: (String) -> Unit) {

    val intent = Intent(RecognizerIntent.ACTION_RECOGNIZE_SPEECH)
    intent.putExtra(RecognizerIntent.EXTRA_LANGUAGE_MODEL, RecognizerIntent.LANGUAGE_MODEL_FREE_FORM)
    intent.putExtra(RecognizerIntent.EXTRA_LANGUAGE, Locale.getDefault())
    intent.putExtra(RecognizerIntent.EXTRA_PROMPT, "Speak to Maretlagadi Assistant")

    try {
        val speechRecognizer = SpeechRecognizer.createSpeechRecognizer(context)

        speechRecognizer.setRecognitionListener(object : android.speech.RecognitionListener {
            override fun onReadyForSpeech(params: Bundle?) {}
            override fun onBeginningOfSpeech() {}
            override fun onRmsChanged(rmsdB: Float) {}
            override fun onBufferReceived(buffer: ByteArray?) {}
            override fun onEndOfSpeech() {}

            override fun onError(error: Int) {
                speechRecognizer.destroy()
                onResult("")
            }

            override fun onResults(results: Bundle?) {
                val matches = results?.getStringArrayList(SpeechRecognizer.RESULTS_RECOGNITION)
                val spokenText = matches?.firstOrNull() ?: ""
                speechRecognizer.destroy()
                onResult(spokenText)
            }

            override fun onPartialResults(partialResults: Bundle?) {}
            override fun onEvent(eventType: Int, params: Bundle?) {}
        })

        speechRecognizer.startListening(intent)

    } catch (e: Exception) {
        onResult("")
    }
}