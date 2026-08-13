package com.maretlagadi.welfarecentre.ui.events

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.databinding.FragmentEventsBinding
import com.maretlagadi.welfarecentre.ui.common.TwoLineAdapter
import com.maretlagadi.welfarecentre.ui.common.TwoLineItem
import kotlinx.coroutines.launch

/** Shows all organisational events, with date and location as described in 1.4 "Viewing Programmes and Events". */
class EventsFragment : Fragment() {

    private var _binding: FragmentEventsBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentEventsBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        val adapter = TwoLineAdapter()
        binding.recyclerView.layoutManager = LinearLayoutManager(requireContext())
        binding.recyclerView.adapter = adapter

        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.getAllEvents().collect { events ->
                binding.tvEmpty.visibility = if (events.isEmpty()) View.VISIBLE else View.GONE
                adapter.submitList(events.map {
                    TwoLineItem(id = it.eventId, title = it.name, subtitle = it.location, meta = it.date)
                })
            }
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
