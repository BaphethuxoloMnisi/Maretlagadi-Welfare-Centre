package com.maretlagadi.welfarecentre.ui.admin

import android.app.AlertDialog
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.data.entities.User
import com.maretlagadi.welfarecentre.databinding.FragmentAdminListBinding
import com.maretlagadi.welfarecentre.ui.common.TwoLineActionableAdapter
import com.maretlagadi.welfarecentre.ui.common.TwoLineItem
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

/** Admin: manage users - covers "Managing the System as an Administrator" (users), including each user's last login time for admin visibility. */
class AdminUsersFragment : Fragment() {

    private var _binding: FragmentAdminListBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }
    private var usersById: Map<Long, User> = emptyMap()
    private val dateFormat = SimpleDateFormat("dd MMM yyyy, HH:mm", Locale.getDefault())

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentAdminListBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        binding.tvTitle.text = "Manage Users"

        val adapter = TwoLineActionableAdapter { item ->
            val user = usersById[item.id] ?: return@TwoLineActionableAdapter
            AlertDialog.Builder(requireContext())
                .setTitle("Remove user")
                .setMessage("Remove ${user.name}? This cannot be undone.")
                .setPositiveButton("Remove") { _, _ ->
                    viewLifecycleOwner.lifecycleScope.launch { app.repository.deleteUser(user) }
                }
                .setNegativeButton("Cancel", null)
                .show()
        }
        binding.recyclerView.layoutManager = LinearLayoutManager(requireContext())
        binding.recyclerView.adapter = adapter

        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.getAllUsers().collect { users ->
                usersById = users.associateBy { it.userId }
                binding.tvEmpty.visibility = if (users.isEmpty()) View.VISIBLE else View.GONE
                adapter.submitList(users.map {
                    val lastLogin = it.lastLoginAt?.let { t -> "Last login: ${dateFormat.format(Date(t))}" } ?: "Never logged in"
                    TwoLineItem(id = it.userId, title = it.name, subtitle = "${it.email} · ${it.role.name}\n$lastLogin")
                })
            }
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
