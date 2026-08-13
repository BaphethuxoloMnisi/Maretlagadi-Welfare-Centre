package com.maretlagadi.welfarecentre.ui.profile

import android.app.AlertDialog
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.navigation.fragment.findNavController
import com.maretlagadi.welfarecentre.R
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.data.entities.User
import com.maretlagadi.welfarecentre.databinding.FragmentProfileBinding
import kotlinx.coroutines.launch

/** Profile screen (wireframe 9.6): read-only summary + a settings gear for actions. */
class ProfileFragment : Fragment() {

    private var _binding: FragmentProfileBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }
    private var currentUser: User? = null

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentProfileBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        val session = app.sessionManager
        if (!session.isLoggedIn()) {
            binding.groupLoggedOut.visibility = View.VISIBLE
            binding.groupLoggedIn.visibility = View.GONE
            binding.btnSettings.visibility = View.GONE
            binding.btnGoToLogin.setOnClickListener {
                findNavController().navigate(R.id.loginFragment)
            }
            return
        }

        binding.groupLoggedOut.visibility = View.GONE
        binding.groupLoggedIn.visibility = View.VISIBLE
        binding.btnSettings.visibility = View.VISIBLE
        binding.btnSettings.setOnClickListener {
            findNavController().navigate(R.id.action_profile_to_settings)
        }

        viewLifecycleOwner.lifecycleScope.launch {
            currentUser = app.repository.getUser(session.getUserId())
            currentUser?.let { user ->
                binding.tvName.text = user.name
                binding.tvEmail.text = user.email
                binding.tvPhone.text = user.phone
                binding.tvRole.text = user.role.name.lowercase().replaceFirstChar { it.uppercase() }
            }
        }

        binding.rowPersonalDetails.setOnClickListener {
            findNavController().navigate(R.id.action_profile_to_settings)
        }
        binding.rowPreferences.setOnClickListener {
            showInfoDialog("Preferences", "Notification and app preferences can be managed from Settings.")
        }
        binding.rowActivityHistory.setOnClickListener {
            viewLifecycleOwner.lifecycleScope.launch {
                val volunteer = app.repository.getVolunteerForUser(session.getUserId())
                val message = if (volunteer != null) {
                    "Total hours contributed: ${volunteer.totalHours}\nCertificates earned: ${volunteer.certificates}"
                } else {
                    "Your enquiries, donations and sign-ups will appear here once you've been active on the app."
                }
                showInfoDialog("Activity History", message)
            }
        }
        binding.rowVolunteeringRecords.setOnClickListener {
            viewLifecycleOwner.lifecycleScope.launch {
                val volunteer = app.repository.getVolunteerForUser(session.getUserId())
                val message = if (volunteer != null) {
                    "Skills: ${volunteer.skills}\nAvailability: ${volunteer.availability}"
                } else {
                    "You're not registered as a volunteer yet - visit the Volunteer tab to sign up."
                }
                showInfoDialog("Volunteering Records", message)
            }
        }
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
