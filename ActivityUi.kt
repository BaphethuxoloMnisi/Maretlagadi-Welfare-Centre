package com.maretlagadi.welfarecentre.ui.auth

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.maretlagadi.welfarecentre.data.entities.User
import com.maretlagadi.welfarecentre.repository.WelfareRepository
import kotlinx.coroutines.launch

sealed class AuthResult {
    data class Success(val user: User) : AuthResult()
    data class Failure(val message: String) : AuthResult()
}

/**
 * Shared by Login, Register and Forgot Password screens since they all
 * revolve around the same repository operations.
 */
class AuthViewModel(private val repository: WelfareRepository) : ViewModel() {

    private val _loading = MutableLiveData(false)
    val loading: LiveData<Boolean> = _loading

    private val _authResult = MutableLiveData<AuthResult?>()
    val authResult: LiveData<AuthResult?> = _authResult

    private val _resetComplete = MutableLiveData<Boolean?>()
    val resetComplete: LiveData<Boolean?> = _resetComplete

    fun login(email: String, password: String) {
        viewModelScope.launch {
            _loading.value = true
            val user = repository.login(email.trim(), password)
            _loading.value = false
            _authResult.value = if (user != null) {
                AuthResult.Success(user)
            } else {
                AuthResult.Failure("Incorrect email or password. Please try again.")
            }
        }
    }

    fun register(name: String, email: String, phone: String, password: String) {
        viewModelScope.launch {
            _loading.value = true
            val newUserId = repository.register(name.trim(), email.trim(), phone.trim(), password)
            _loading.value = false
            if (newUserId != null) {
                val user = repository.getUser(newUserId)
                _authResult.value = if (user != null) AuthResult.Success(user)
                else AuthResult.Failure("Something went wrong. Please try again.")
            } else {
                _authResult.value = AuthResult.Failure("An account with this email already exists.")
            }
        }
    }

    fun resetPassword(email: String, newPassword: String) {
        viewModelScope.launch {
            _loading.value = true
            val success = repository.resetPassword(email.trim(), newPassword)
            _loading.value = false
            _resetComplete.value = success
        }
    }

    fun clearResult() {
        _authResult.value = null
        _resetComplete.value = null
    }
}
