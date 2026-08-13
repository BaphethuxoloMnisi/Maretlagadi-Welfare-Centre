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
import com.maretlagadi.welfarecentre.databinding.FragmentLoginBinding
import com.maretlagadi.welfarecentre.utils.InputValidator

class LoginFragment : Fragment() {

    private var _binding: FragmentLoginBinding? = null
    private val binding get() = _binding!!

    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }

    private val viewModel: AuthViewModel by viewModels {
        AuthViewModelFactory(app.repository)
    }

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentLoginBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        binding.btnLogin.setOnClickListener { attemptLogin() }
        binding.tvGoToRegister.setOnClickListener {
            findNavController().navigate(R.id.action_login_to_register)
        }
        binding.tvForgotPassword.setOnClickListener {
            findNavController().navigate(R.id.action_login_to_forgot)
        }
        binding.tvContinueAsGuest.setOnClickListener {
            findNavController().navigate(R.id.action_login_to_home)
        }

        viewModel.loading.observe(viewLifecycleOwner) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.btnLogin.isEnabled = !isLoading
        }

        viewModel.authResult.observe(viewLifecycleOwner) { result ->
            when (result) {
                is AuthResult.Success -> {
                    app.sessionManager.saveSession(result.user.userId, result.user.role)
                    viewModel.clearResult()
                    findNavController().navigate(R.id.action_login_to_home)
                }
                is AuthResult.Failure -> {
                    binding.tvError.text = result.message
                    binding.tvError.visibility = View.VISIBLE
                }
                null -> Unit
            }
        }
    }

    private fun attemptLogin() {
        binding.tvError.visibility = View.GONE
        val email = binding.etEmail.text.toString().trim()
        val password = binding.etPassword.text.toString()

        if (!InputValidator.isValidEmail(email)) {
            binding.tvError.text = "Please enter a valid email address."
            binding.tvError.visibility = View.VISIBLE
            return
        }
        if (password.isEmpty()) {
            binding.tvError.text = "Please enter your password."
            binding.tvError.visibility = View.VISIBLE
            return
        }
        viewModel.login(email, password)
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
