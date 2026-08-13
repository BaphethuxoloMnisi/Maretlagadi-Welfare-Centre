package com.maretlagadi.welfarecentre.ui.notifications

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.tabs.TabLayout
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.data.entities.Notification
import com.maretlagadi.welfarecentre.data.entities.NotificationType
import com.maretlagadi.welfarecentre.databinding.FragmentNotificationsBinding
import kotlinx.coroutines.launch

/** Notifications screen with All / Unread / Updates / Alerts tabs (wireframe 9.3). */
class NotificationsFragment : Fragment() {

    private var _binding: FragmentNotificationsBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }

    private var allNotifications: List<Notification> = emptyList()
    private var selectedTab = 0
    private lateinit var adapter: NotificationAdapter

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentNotificationsBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        adapter = NotificationAdapter { notification ->
            viewLifecycleOwner.lifecycleScope.launch { app.repository.markNotificationRead(notification) }
        }
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

        val session = app.sessionManager
        val flow = if (session.isLoggedIn()) {
            app.repository.getNotificationsForUser(session.getUserId())
        } else {
            app.repository.getAllNotifications()
        }

        viewLifecycleOwner.lifecycleScope.launch {
            flow.collect { notifications ->
                allNotifications = notifications
                applyFilter()
            }
        }
    }

    private fun applyFilter() {
        val filtered = when (selectedTab) {
            1 -> allNotifications.filter { it.status == "UNREAD" }
            2 -> allNotifications.filter { it.type == NotificationType.UPDATE }
            3 -> allNotifications.filter { it.type == NotificationType.ALERT }
            else -> allNotifications
        }
        binding.tvEmpty.visibility = if (filtered.isEmpty()) View.VISIBLE else View.GONE
        adapter.submitList(filtered)
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
