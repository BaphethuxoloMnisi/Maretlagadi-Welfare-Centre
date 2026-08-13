package com.maretlagadi.welfarecentre.ui.donations

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.databinding.FragmentDonationsBinding
import com.maretlagadi.welfarecentre.utils.InputValidator
import kotlinx.coroutines.launch

/**
 * Donation screen (wireframe 9.9): quick-amount buttons plus a custom
 * amount field. Per the FAQ in the documentation, the initial scope does
 * not include a full e-commerce/payment system - this screen provides
 * banking details and lets the user log a reference for a donation made
 * externally, which is stored for admin follow-up.
 */
class DonationsFragment : Fragment() {

    private var _binding: FragmentDonationsBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentDonationsBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        val quickButtons = listOf(binding.btnAmount10, binding.btnAmount30, binding.btnAmount50)
        val amounts = listOf("10", "30", "50")
        quickButtons.forEachIndexed { index, button ->
            button.setOnClickListener {
                binding.etAmount.setText(amounts[index])
            }
        }

        binding.btnSubmit.setOnClickListener { logDonation() }
    }

    private fun logDonation() {
        binding.tvError.visibility = View.GONE
        val name = binding.etDonorName.text.toString().trim()
        val amountText = binding.etAmount.text.toString().trim()
        val reference = binding.etReference.text.toString().trim()
        val amount = amountText.toDoubleOrNull()

        val error = when {
            !InputValidator.isNotBlank(name) -> "Please enter your name."
            amount == null || amount <= 0 -> "Please select or enter a valid donation amount."
            !InputValidator.isNotBlank(reference) -> "Please enter your payment reference."
            else -> null
        }

        if (error != null) {
            binding.tvError.text = error
            binding.tvError.visibility = View.VISIBLE
            return
        }

        val userId = app.sessionManager.getUserId().takeIf { it != -1L }
        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.recordDonation(userId, name, amount!!, reference)
            Toast.makeText(requireContext(), "Thank you! Your donation has been logged.", Toast.LENGTH_LONG).show()
            binding.etAmount.setText("")
            binding.etReference.setText("")
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
