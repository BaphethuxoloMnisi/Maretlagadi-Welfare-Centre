package com.maretlagadi.welfarecentre

import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.navigation.NavController
import androidx.navigation.fragment.NavHostFragment
import androidx.navigation.ui.setupWithNavController
import com.maretlagadi.welfarecentre.databinding.ActivityMainBinding

/**
 * Single-activity host. A NavHostFragment drives all screens; the bottom
 * navigation bar (Home, Programmes, Volunteer, Calendar, Profile) is wired
 * to the nav graph and hidden on the splash/login/register screens.
 */
class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding
    private lateinit var navController: NavController

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        val navHostFragment =
            supportFragmentManager.findFragmentById(R.id.navHostFragment) as NavHostFragment
        navController = navHostFragment.navController

        binding.bottomNav.setupWithNavController(navController)

        val topLevelDestinations = setOf(
            R.id.homeFragment, R.id.programmesFragment, R.id.volunteerFragment,
            R.id.calendarFragment, R.id.profileFragment
        )

        navController.addOnDestinationChangedListener { _, destination, _ ->
            binding.bottomNav.visibility =
                if (destination.id in topLevelDestinations) android.view.View.VISIBLE else android.view.View.GONE
        }
    }

    override fun onSupportNavigateUp(): Boolean {
        return navController.navigateUp() || super.onSupportNavigateUp()
    }
}
