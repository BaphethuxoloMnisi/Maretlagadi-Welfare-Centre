package com.maretlagadi.welfarecentre.ui.admin

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.data.entities.Programme
import com.maretlagadi.welfarecentre.databinding.FragmentAdminProgrammesBinding
import com.maretlagadi.welfarecentre.ui.common.TwoLineActionableAdapter
import com.maretlagadi.welfarecentre.ui.common.TwoLineItem
import com.maretlagadi.welfarecentre.utils.InputValidator
import kotlinx.coroutines.launch

/** Admin: Programme and Event Management Module - programmes side. */
class AdminProgrammesFragment : Fragment() {

    private var _binding: FragmentAdminProgrammesBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }
    private var programmesById: Map<Long, Programme> = emptyMap()

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentAdminProgrammesBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        val adapter = TwoLineActionableAdapter { item ->
            val programme = programmesById[item.id] ?: return@TwoLineActionableAdapter
            viewLifecycleOwner.lifecycleScope.launch { app.repository.deleteProgramme(programme) }
        }
        binding.recyclerView.layoutManager = LinearLayoutManager(requireContext())
        binding.recyclerView.adapter = adapter

        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.getAllProgrammes().collect { programmes ->
                programmesById = programmes.associateBy { it.programmeId }
                adapter.submitList(programmes.map {
                    TwoLineItem(id = it.programmeId, title = it.title, subtitle = it.description)
                })
            }
        }

        binding.btnAdd.setOnClickListener {
            val title = binding.etTitle.text.toString().trim()
            val description = binding.etDescription.text.toString().trim()
            if (!InputValidator.isNotBlank(title) || !InputValidator.isNotBlank(description)) {
                Toast.makeText(requireContext(), "Please enter a title and description.", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }
            viewLifecycleOwner.lifecycleScope.launch {
                app.repository.addProgramme(title, description)
                binding.etTitle.setText("")
                binding.etDescription.setText("")
            }
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
