package com.maretlagadi.welfarecentre.ui.admin

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.data.entities.Event
import com.maretlagadi.welfarecentre.data.entities.Programme
import com.maretlagadi.welfarecentre.databinding.FragmentAdminEventsBinding
import com.maretlagadi.welfarecentre.ui.common.TwoLineActionableAdapter
import com.maretlagadi.welfarecentre.ui.common.TwoLineItem
import com.maretlagadi.welfarecentre.utils.InputValidator
import kotlinx.coroutines.launch

/** Admin: Programme and Event Management Module - events side. */
class AdminEventsFragment : Fragment() {

    private var _binding: FragmentAdminEventsBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }

    private var eventsById: Map<Long, Event> = emptyMap()
    private var programmes: List<Programme> = emptyList()

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentAdminEventsBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        val adapter = TwoLineActionableAdapter { item ->
            val event = eventsById[item.id] ?: return@TwoLineActionableAdapter
            viewLifecycleOwner.lifecycleScope.launch { app.repository.deleteEvent(event) }
        }
        binding.recyclerView.layoutManager = LinearLayoutManager(requireContext())
        binding.recyclerView.adapter = adapter

        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.getAllProgrammes().collect { list ->
                programmes = list
                val spinnerAdapter = ArrayAdapter(
                    requireContext(),
                    android.R.layout.simple_spinner_item,
                    list.map { it.title }
                )
                spinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                binding.spinnerProgramme.adapter = spinnerAdapter
            }
        }

        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.getAllEvents().collect { events ->
                eventsById = events.associateBy { it.eventId }
                val programmeTitleById = programmes.associateBy({ it.programmeId }, { it.title })
                adapter.submitList(events.map {
                    TwoLineItem(
                        id = it.eventId,
                        title = it.name,
                        subtitle = "${programmeTitleById[it.programmeId] ?: "Unknown programme"} · ${it.location} · ${it.date}"
                    )
                })
            }
        }

        binding.btnAdd.setOnClickListener { addEvent() }
    }

    private fun addEvent() {
        val name = binding.etName.text.toString().trim()
        val date = binding.etDate.text.toString().trim()
        val location = binding.etLocation.text.toString().trim()
        val selectedIndex = binding.spinnerProgramme.selectedItemPosition

        if (programmes.isEmpty() || selectedIndex < 0) {
            Toast.makeText(requireContext(), "Please add a programme first.", Toast.LENGTH_SHORT).show()
            return
        }
        if (!InputValidator.isNotBlank(name) || !InputValidator.isNotBlank(date) || !InputValidator.isNotBlank(location)) {
            Toast.makeText(requireContext(), "Please fill in all event fields.", Toast.LENGTH_SHORT).show()
            return
        }

        val programmeId = programmes[selectedIndex].programmeId
        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.addEvent(programmeId, name, date, location)
            binding.etName.setText("")
            binding.etDate.setText("")
            binding.etLocation.setText("")
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
