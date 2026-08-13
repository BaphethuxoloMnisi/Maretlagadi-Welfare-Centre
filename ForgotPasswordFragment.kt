package com.maretlagadi.welfarecentre.ui.admin

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.navigation.fragment.findNavController
import com.maretlagadi.welfarecentre.R
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.databinding.FragmentAdminDashboardBinding
import kotlinx.coroutines.flow.combine
import kotlinx.coroutines.launch

/**
 * Central admin area, per "Managing the System as an Administrator" (1.4).
 * Access is guarded here since a public/volunteer user could otherwise
 * deep-link to this destination.
 */
class AdminDashboardFragment : Fragment() {

    private var _binding: FragmentAdminDashboardBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentAdminDashboardBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        if (!app.sessionManager.isAdmin()) {
            findNavController().navigateUp()
            return
        }

        binding.cardUsers.setOnClickListener { findNavController().navigate(R.id.action_admin_to_users) }
        binding.cardProgrammes.setOnClickListener { findNavController().navigate(R.id.action_admin_to_programmes) }
        binding.cardEvents.setOnClickListener { findNavController().navigate(R.id.action_admin_to_events) }
        binding.cardEnquiries.setOnClickListener { findNavController().navigate(R.id.action_admin_to_enquiries) }
        binding.cardActivity.setOnClickListener { findNavController().navigate(R.id.action_admin_to_activity) }

        viewLifecycleOwner.lifecycleScope.launch {
            combine(
                app.repository.getAllUsers(),
                app.repository.getAllVolunteers(),
                app.repository.getAllProgrammes(),
                app.repository.getAllEvents()
            ) { users, volunteers, programmes, events ->
                "${users.size} registered users · ${volunteers.size} volunteers · " +
                    "${programmes.size} programmes · ${events.size} events"
            }.collect { binding.tvStats.text = it }
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
