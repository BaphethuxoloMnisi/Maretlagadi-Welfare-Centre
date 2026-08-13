package com.maretlagadi.welfarecentre

import android.app.Application
import com.maretlagadi.welfarecentre.data.AppDatabase
import com.maretlagadi.welfarecentre.repository.WelfareRepository
import com.maretlagadi.welfarecentre.utils.SessionManager

/**
 * Application class that lazily builds the single Room database instance,
 * the repository built on top of it, and the session manager - shared
 * across all fragments/ViewModels via (application as WelfareApp).
 */
class WelfareApp : Application() {

    val database: AppDatabase by lazy { AppDatabase.getInstance(this) }
    val repository: WelfareRepository by lazy { WelfareRepository(database) }
    val sessionManager: SessionManager by lazy { SessionManager(this) }
}
