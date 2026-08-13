package com.maretlagadi.welfarecentre.ui.admin

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.databinding.FragmentAdminListBinding
import com.maretlagadi.welfarecentre.ui.common.TwoLineAdapter
import com.maretlagadi.welfarecentre.ui.common.TwoLineItem
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

/** Admin: read-only view of Contact Us enquiries submitted by visitors. */
class AdminEnquiriesFragment : Fragment() {

    private var _binding: FragmentAdminListBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentAdminListBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        binding.tvTitle.text = "Enquiries"

        val adapter = TwoLineAdapter()
        binding.recyclerView.layoutManager = LinearLayoutManager(requireContext())
        binding.recyclerView.adapter = adapter

        val dateFormat = SimpleDateFormat("dd MMM yyyy, HH:mm", Locale.getDefault())

        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.getAllEnquiries().collect { messages ->
                binding.tvEmpty.visibility = if (messages.isEmpty()) View.VISIBLE else View.GONE
                adapter.submitList(messages.map {
                    TwoLineItem(
                        id = it.messageId,
                        title = "${it.senderName} (${it.senderEmail})",
                        subtitle = it.content,
                        meta = dateFormat.format(Date(it.dateSent))
                    )
                })
            }
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
