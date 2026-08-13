package com.maretlagadi.welfarecentre.ui.admin

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.tabs.TabLayout
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.data.entities.ActivityLog
import com.maretlagadi.welfarecentre.data.entities.ActivityType
import com.maretlagadi.welfarecentre.databinding.FragmentAdminActivityBinding
import kotlinx.coroutines.launch

/**
 * Admin-only audit trail: every significant action a user takes in the
 * app - not just donations and logins, but registrations, volunteering,
 * shift responses, enquiries, and account changes too.
 */
class AdminActivityFragment : Fragment() {

    private var _binding: FragmentAdminActivityBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }

    private var allLogs: List<ActivityLog> = emptyList()
    private var selectedTab = 0
    private lateinit var adapter: AdminActivityAdapter

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentAdminActivityBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        if (!app.sessionManager.isAdmin()) {
            findNavController().navigateUp()
            return
        }

        adapter = AdminActivityAdapter()
        binding.recyclerView.layoutManager = LinearLayoutManager(requireContext())
        binding.recyclerView.adapter = adapter

        binding.tabLayout.addOnTabSelectedListener(object : TabLayout.OnTabSelectedListener {
            override fun onTabSelected(tab: TabLayout.Tab) {
                selectedTab = tab.position
                applyFilter()
            }
            override fun onTabUnselected(tab: TabLayout.Tab) = Unit
            override fun onTabReselected(tab: TabLayout.Tab) = Unit
        })

        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.getAllActivityLogs().collect { logs ->
                allLogs = logs
                applyFilter()
            }
        }
    }

    private fun applyFilter() {
        val filtered = when (selectedTab) {
            1 -> allLogs.filter { it.type == ActivityType.LOGIN || it.type == ActivityType.LOGOUT }
            2 -> allLogs.filter { it.type == ActivityType.DONATION_LOGGED }
            3 -> allLogs.filter {
                it.type == ActivityType.VOLUNTEER_REGISTERED || it.type == ActivityType.SHIFT_SIGNUP ||
                    it.type == ActivityType.SHIFT_ACCEPTED || it.type == ActivityType.SHIFT_DECLINED
            }
            4 -> allLogs.filter { it.type == ActivityType.ENQUIRY_SUBMITTED }
            5 -> allLogs.filter {
                it.type == ActivityType.REGISTER || it.type == ActivityType.PROFILE_UPDATED ||
                    it.type == ActivityType.PASSWORD_CHANGED || it.type == ActivityType.PASSWORD_RESET
            }
            else -> allLogs
        }
        binding.tvEmpty.visibility = if (filtered.isEmpty()) View.VISIBLE else View.GONE
        adapter.submitList(filtered)
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
