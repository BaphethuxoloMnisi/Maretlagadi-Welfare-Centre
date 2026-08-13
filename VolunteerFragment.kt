package com.maretlagadi.welfarecentre.ui.auth

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.fragment.app.viewModels
import androidx.navigation.fragment.findNavController
import com.maretlagadi.welfarecentre.R
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.databinding.FragmentRegisterBinding
import com.maretlagadi.welfarecentre.utils.InputValidator

class RegisterFragment : Fragment() {

    private var _binding: FragmentRegisterBinding? = null
    private val binding get() = _binding!!

    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }
    private val viewModel: AuthViewModel by viewModels { AuthViewModelFactory(app.repository) }

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentRegisterBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        binding.btnRegister.setOnClickListener { attemptRegister() }
        binding.tvGoToLogin.setOnClickListener {
            findNavController().navigate(R.id.action_register_to_login)
        }

        viewModel.loading.observe(viewLifecycleOwner) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.btnRegister.isEnabled = !isLoading
        }

        viewModel.authResult.observe(viewLifecycleOwner) { result ->
            when (result) {
                is AuthResult.Success -> {
                    app.sessionManager.saveSession(result.user.userId, result.user.role)
                    viewModel.clearResult()
                    findNavController().navigate(R.id.action_register_to_home)
                }
                is AuthResult.Failure -> {
                    binding.tvError.text = result.message
                    binding.tvError.visibility = View.VISIBLE
                }
                null -> Unit
            }
        }
    }

    private fun attemptRegister() {
        binding.tvError.visibility = View.GONE
        val name = binding.etName.text.toString().trim()
        val email = binding.etEmail.text.toString().trim()
        val phone = binding.etPhone.text.toString().trim()
        val password = binding.etPassword.text.toString()
        val confirm = binding.etConfirmPassword.text.toString()

        val error = when {
            !InputValidator.isNotBlank(name) -> "Please enter your full name."
            !InputValidator.isValidEmail(email) -> "Please enter a valid email address."
            !InputValidator.isValidPhone(phone) -> "Please enter a valid telephone number."
            !InputValidator.isValidPassword(password) ->
                "Password must be at least 8 characters and include a letter and a number."
            password != confirm -> "Passwords do not match."
            else -> null
        }

        if (error != null) {
            binding.tvError.text = error
            binding.tvError.visibility = View.VISIBLE
            return
        }

        viewModel.register(name, email, phone, password)
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
