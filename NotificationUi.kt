package com.maretlagadi.welfarecentre.ui.auth

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.fragment.app.viewModels
import androidx.navigation.fragment.findNavController
import com.maretlagadi.welfarecentre.R
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.databinding.FragmentForgotPasswordBinding
import com.maretlagadi.welfarecentre.utils.InputValidator

class ForgotPasswordFragment : Fragment() {

    private var _binding: FragmentForgotPasswordBinding? = null
    private val binding get() = _binding!!

    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }
    private val viewModel: AuthViewModel by viewModels { AuthViewModelFactory(app.repository) }

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentForgotPasswordBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        binding.btnReset.setOnClickListener {
            binding.tvError.visibility = View.GONE
            val email = binding.etEmail.text.toString().trim()
            val newPassword = binding.etNewPassword.text.toString()

            val error = when {
                !InputValidator.isValidEmail(email) -> "Please enter a valid email address."
                !InputValidator.isValidPassword(newPassword) ->
                    "Password must be at least 8 characters and include a letter and a number."
                else -> null
            }

            if (error != null) {
                binding.tvError.text = error
                binding.tvError.visibility = View.VISIBLE
                return@setOnClickListener
            }
            viewModel.resetPassword(email, newPassword)
        }

        viewModel.resetComplete.observe(viewLifecycleOwner) { success ->
            when (success) {
                true -> {
                    Toast.makeText(requireContext(), "Password updated. Please log in.", Toast.LENGTH_LONG).show()
                    viewModel.clearResult()
                    findNavController().navigate(R.id.action_forgot_to_login)
                }
                false -> {
                    binding.tvError.text = "No account was found with that email address."
                    binding.tvError.visibility = View.VISIBLE
                }
                null -> Unit
            }
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
