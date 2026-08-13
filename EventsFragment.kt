package com.maretlagadi.welfarecentre.ui.profile

import android.app.AlertDialog
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.appcompat.app.AppCompatDelegate
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.navigation.fragment.findNavController
import com.maretlagadi.welfarecentre.R
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.data.entities.User
import com.maretlagadi.welfarecentre.databinding.FragmentSettingsBinding
import com.maretlagadi.welfarecentre.utils.InputValidator
import kotlinx.coroutines.launch

/**
 * Settings screen (wireframe 9.10): account actions (edit profile, change
 * password), notification/app preferences, and logout. Split out from the
 * read-only Profile screen so Profile stays a summary and Settings holds
 * the actions, matching the wireframe.
 */
class SettingsFragment : Fragment() {

    private var _binding: FragmentSettingsBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }
    private var currentUser: User? = null

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentSettingsBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        binding.btnBack.setOnClickListener { findNavController().navigateUp() }

        viewLifecycleOwner.lifecycleScope.launch {
            currentUser = app.repository.getUser(app.sessionManager.getUserId())
            currentUser?.let { user ->
                binding.etName.setText(user.name)
                binding.etPhone.setText(user.phone)
            }
        }

        binding.rowEditProfile.setOnClickListener { toggle(binding.groupEditProfile) }
        binding.rowChangePassword.setOnClickListener { toggle(binding.groupChangePassword) }
        binding.rowPrivacy.setOnClickListener {
            showInfoDialog(
                "Privacy Settings",
                "Your personal information is only used to manage your account, volunteering and donations at Maretlagadi Welfare Centre, per the POPIA note in section 2.6 of the project documentation."
            )
        }
        binding.rowAboutUs.setOnClickListener {
            showInfoDialog(
                "About Us",
                "Maretlagadi Welfare Centre supports the local community through programmes in early childhood development, food aid, elderly care and youth skills development."
            )
        }
        binding.rowLogout.setOnClickListener { confirmLogout() }

        binding.btnSaveProfile.setOnClickListener { saveProfile() }
        binding.btnChangePassword.setOnClickListener { changePassword() }

        binding.switchDarkMode.setOnCheckedChangeListener { _, isChecked ->
            AppCompatDelegate.setDefaultNightMode(
                if (isChecked) AppCompatDelegate.MODE_NIGHT_YES else AppCompatDelegate.MODE_NIGHT_NO
            )
        }
    }

    private fun toggle(group: View) {
        group.visibility = if (group.visibility == View.VISIBLE) View.GONE else View.VISIBLE
    }

    private fun saveProfile() {
        binding.tvSettingsError.visibility = View.GONE
        val name = binding.etName.text.toString().trim()
        val phone = binding.etPhone.text.toString().trim()

        val error = when {
            !InputValidator.isNotBlank(name) -> "Please enter your full name."
            !InputValidator.isValidPhone(phone) -> "Please enter a valid telephone number."
            else -> null
        }
        if (error != null) {
            binding.tvSettingsError.text = error
            binding.tvSettingsError.visibility = View.VISIBLE
            return
        }

        val user = currentUser ?: return
        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.updateProfile(user.copy(name = name, phone = phone))
            Toast.makeText(requireContext(), "Profile updated.", Toast.LENGTH_SHORT).show()
        }
    }

    private fun changePassword() {
        val newPassword = binding.etNewPassword.text.toString()
        if (!InputValidator.isValidPassword(newPassword)) {
            binding.tvSettingsError.text = "Password must be at least 8 characters and include a letter and a number."
            binding.tvSettingsError.visibility = View.VISIBLE
            return
        }
        binding.tvSettingsError.visibility = View.GONE
        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.changePassword(app.sessionManager.getUserId(), newPassword)
            binding.etNewPassword.setText("")
            Toast.makeText(requireContext(), "Password updated.", Toast.LENGTH_SHORT).show()
        }
    }

    private fun confirmLogout() {
        AlertDialog.Builder(requireContext())
            .setTitle("Logout")
            .setMessage("Are you sure you want to log out?")
            .setPositiveButton("Logout") { _, _ ->
                viewLifecycleOwner.lifecycleScope.launch {
                    app.repository.logLogout(app.sessionManager.getUserId())
                    app.sessionManager.logout()
                    findNavController().navigate(R.id.action_settings_to_login)
                }
            }
            .setNegativeButton("Cancel", null)
            .show()
    }

    private fun showInfoDialog(title: String, message: String) {
        AlertDialog.Builder(requireContext())
            .setTitle(title)
            .setMessage(message)
            .setPositiveButton("OK", null)
            .show()
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
