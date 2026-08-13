package com.maretlagadi.welfarecentre.repository

import com.maretlagadi.welfarecentre.data.AppDatabase
import com.maretlagadi.welfarecentre.data.entities.*
import com.maretlagadi.welfarecentre.utils.PasswordUtils
import kotlinx.coroutines.flow.Flow

/**
 * Single access point for all data operations. Fragments/ViewModels talk to
 * this class instead of DAOs directly, keeping the persistence layer
 * (Room/SQLite today) swappable for a remote API later without changing UI
 * code - matching the layered client-server architecture described in
 * section 2.1 of the documentation.
 */
class WelfareRepository(private val database: AppDatabase) {

    // ---------- Authentication / Users ----------

    /** Returns the new user id, or null if the email is already registered. */
    suspend fun register(name: String, email: String, phone: String, password: String): Long? {
        if (database.userDao().countByEmail(email) > 0) return null
        val (hash, salt) = PasswordUtils.hashPassword(password)
        val newId = database.userDao().insert(
            User(
                name = name,
                email = email,
                phone = phone,
                passwordHash = hash,
                passwordSalt = salt,
                role = UserRole.PUBLIC_USER
            )
        )
        logActivity(newId, name, email, ActivityType.REGISTER, "Created a new account")
        return newId
    }

    /** Returns the authenticated user, or null if credentials are invalid. Records the login time for admin visibility. */
    suspend fun login(email: String, password: String): User? {
        val user = database.userDao().findByEmail(email) ?: return null
        if (!PasswordUtils.verifyPassword(password, user.passwordHash, user.passwordSalt)) return null
        val updated = user.copy(lastLoginAt = System.currentTimeMillis())
        database.userDao().update(updated)
        logActivity(updated.userId, updated.name, updated.email, ActivityType.LOGIN, "Logged in")
        return updated
    }

    /** Called explicitly before a session is cleared, since logout itself is a client-side/SessionManager action. */
    suspend fun logLogout(userId: Long) {
        val user = database.userDao().findById(userId) ?: return
        logActivity(user.userId, user.name, user.email, ActivityType.LOGOUT, "Logged out")
    }

    /** Password reset flow: sets a new password for the account matching the email. */
    suspend fun resetPassword(email: String, newPassword: String): Boolean {
        val user = database.userDao().findByEmail(email) ?: return false
        val (hash, salt) = PasswordUtils.hashPassword(newPassword)
        database.userDao().update(user.copy(passwordHash = hash, passwordSalt = salt))
        return true
    }

    suspend fun changePassword(userId: Long, newPassword: String) {
        val user = database.userDao().findById(userId) ?: return
        val (hash, salt) = PasswordUtils.hashPassword(newPassword)
        database.userDao().update(user.copy(passwordHash = hash, passwordSalt = salt))
        logActivity(user.userId, user.name, user.email, ActivityType.PASSWORD_CHANGED, "Changed their password")
    }

    suspend fun getUser(userId: Long): User? = database.userDao().findById(userId)

    suspend fun updateProfile(user: User) {
        database.userDao().update(user)
        logActivity(user.userId, user.name, user.email, ActivityType.PROFILE_UPDATED, "Updated profile details")
    }

    fun getAllUsers(): Flow<List<User>> = database.userDao().getAllUsers()

    suspend fun deleteUser(user: User) = database.userDao().delete(user)

    suspend fun setUserRole(userId: Long, role: UserRole) {
        val user = database.userDao().findById(userId) ?: return
        database.userDao().update(user.copy(role = role))
    }

    // ---------- Volunteers ----------

    suspend fun registerVolunteer(userId: Long, skills: String, availability: String): Long {
        val user = database.userDao().findById(userId)
        user?.let { database.userDao().update(it.copy(role = UserRole.VOLUNTEER)) }
        val newId = database.volunteerDao().insert(
            Volunteer(userId = userId, skills = skills, availability = availability)
        )
        user?.let {
            logActivity(it.userId, it.name, it.email, ActivityType.VOLUNTEER_REGISTERED, "Registered as a volunteer")
        }
        return newId
    }

    suspend fun getVolunteerForUser(userId: Long): Volunteer? = database.volunteerDao().findByUserId(userId)

    fun getAllVolunteers(): Flow<List<Volunteer>> = database.volunteerDao().getAllVolunteers()

    // ---------- Programmes & Events ----------

    fun getAllProgrammes(): Flow<List<Programme>> = database.programmeDao().getAllProgrammes()

    suspend fun addProgramme(title: String, description: String) =
        database.programmeDao().insert(Programme(title = title, description = description))

    suspend fun deleteProgramme(programme: Programme) = database.programmeDao().delete(programme)

    fun getAllEvents(): Flow<List<Event>> = database.eventDao().getAllEvents()

    fun getEventsForProgramme(programmeId: Long): Flow<List<Event>> =
        database.eventDao().getEventsForProgramme(programmeId)

    suspend fun addEvent(programmeId: Long, name: String, date: String, location: String) =
        database.eventDao().insert(Event(programmeId = programmeId, name = name, date = date, location = location))

    suspend fun deleteEvent(event: Event) = database.eventDao().delete(event)

    // ---------- Volunteer Shifts ----------

    suspend fun signUpForShift(volunteerId: Long, eventId: Long): Long {
        val shiftId = database.volunteerShiftDao().insert(VolunteerShift(volunteerId = volunteerId, eventId = eventId))
        val volunteer = database.volunteerDao().findById(volunteerId)
        val user = volunteer?.let { database.userDao().findById(it.userId) }
        val event = database.eventDao().findById(eventId)
        user?.let {
            logActivity(it.userId, it.name, it.email, ActivityType.SHIFT_SIGNUP, "Signed up for '${event?.name ?: "an event"}'")
        }
        return shiftId
    }

    fun getShiftsForVolunteer(volunteerId: Long): Flow<List<VolunteerShift>> =
        database.volunteerShiftDao().getShiftsForVolunteer(volunteerId)

    suspend fun respondToShift(shift: VolunteerShift, accept: Boolean) {
        database.volunteerShiftDao().update(
            shift.copy(status = if (accept) ShiftStatus.CONFIRMED else ShiftStatus.CANCELLED)
        )
        val volunteer = database.volunteerDao().findById(shift.volunteerId)
        val user = volunteer?.let { database.userDao().findById(it.userId) }
        val event = database.eventDao().findById(shift.eventId)
        user?.let {
            val type = if (accept) ActivityType.SHIFT_ACCEPTED else ActivityType.SHIFT_DECLINED
            val verb = if (accept) "Accepted" else "Declined"
            logActivity(it.userId, it.name, it.email, type, "$verb the shift for '${event?.name ?: "an event"}'")
        }
    }

    // ---------- Notifications ----------

    fun getNotificationsForUser(userId: Long): Flow<List<Notification>> =
        database.notificationDao().getNotificationsForUser(userId)

    fun getAllNotifications(): Flow<List<Notification>> = database.notificationDao().getAllNotifications()

    suspend fun markNotificationRead(notification: Notification) {
        if (notification.status != "READ") {
            database.notificationDao().update(notification.copy(status = "READ"))
        }
    }

    suspend fun sendNotification(userId: Long?, title: String, message: String, type: NotificationType = NotificationType.ANNOUNCEMENT) =
        database.notificationDao().insert(Notification(userId = userId, title = title, message = message, type = type))

    // ---------- Contact / Enquiries ----------

    suspend fun submitEnquiry(senderName: String, senderEmail: String, senderId: Long?, content: String) {
        database.enquiryDao().insert(
            EnquiryMessage(senderName = senderName, senderEmail = senderEmail, senderId = senderId, content = content)
        )
        logActivity(senderId, senderName, senderEmail, ActivityType.ENQUIRY_SUBMITTED, "Submitted a contact enquiry")
    }

    fun getAllEnquiries(): Flow<List<EnquiryMessage>> = database.enquiryDao().getAllMessages()

    // ---------- Donations ----------

    suspend fun recordDonation(userId: Long?, donorName: String, amount: Double, reference: String) {
        database.donationDao().insert(
            Donation(userId = userId, donorName = donorName, amount = amount, reference = reference)
        )
        val email = userId?.let { database.userDao().findById(it)?.email } ?: "N/A"
        logActivity(userId, donorName, email, ActivityType.DONATION_LOGGED, "Logged a donation of R${amount}")
    }

    fun getAllDonations(): Flow<List<Donation>> = database.donationDao().getAllDonations()

    // ---------- Activity log (admin audit trail) ----------

    /** Admin-facing feed of every significant action users take across the app. */
    fun getAllActivityLogs(): Flow<List<ActivityLog>> = database.activityLogDao().getAllLogs()

    private suspend fun logActivity(userId: Long?, userName: String, userEmail: String, type: ActivityType, description: String) {
        database.activityLogDao().insert(
            ActivityLog(userId = userId, userName = userName, userEmail = userEmail, type = type, description = description)
        )
    }
}
