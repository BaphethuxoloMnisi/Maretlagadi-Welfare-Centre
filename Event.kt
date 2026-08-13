package com.maretlagadi.welfarecentre.ui.contact

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.databinding.FragmentContactBinding
import com.maretlagadi.welfarecentre.utils.InputValidator
import kotlinx.coroutines.launch

/** Covers "Sending an Enquiry" (1.4): the public Contact Us form. */
class ContactFragment : Fragment() {

    private var _binding: FragmentContactBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentContactBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        val session = app.sessionManager
        if (session.isLoggedIn()) {
            viewLifecycleOwner.lifecycleScope.launch {
                app.repository.getUser(session.getUserId())?.let { user ->
                    binding.etName.setText(user.name)
                    binding.etEmail.setText(user.email)
                }
            }
        }

        binding.btnSubmit.setOnClickListener { submitEnquiry() }
    }

    private fun submitEnquiry() {
        binding.tvError.visibility = View.GONE
        val name = binding.etName.text.toString().trim()
        val email = binding.etEmail.text.toString().trim()
        val message = binding.etMessage.text.toString().trim()

        val error = when {
            !InputValidator.isNotBlank(name) -> "Please enter your name."
            !InputValidator.isValidEmail(email) -> "Please enter a valid email address."
            !InputValidator.isNotBlank(message) -> "Please enter a message."
            else -> null
        }

        if (error != null) {
            binding.tvError.text = error
            binding.tvError.visibility = View.VISIBLE
            return
        }

        val senderId = app.sessionManager.getUserId().takeIf { it != -1L }
        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.submitEnquiry(name, email, senderId, message)
            Toast.makeText(requireContext(), "Your enquiry has been submitted. Thank you!", Toast.LENGTH_LONG).show()
            binding.etMessage.setText("")
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
