package com.maretlagadi.welfarecentre.ui.programmes

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.tabs.TabLayout
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.data.entities.Programme
import com.maretlagadi.welfarecentre.data.entities.ProgrammeStatus
import com.maretlagadi.welfarecentre.databinding.FragmentProgrammesBinding
import kotlinx.coroutines.launch

/**
 * Shows the organisation's programmes with All/Ongoing/Upcoming/Past tabs
 * (wireframe 9.5), to any visitor logged in or not - matching "Public
 * users do not need to create an account to access general information"
 * (section 1.1).
 */
class ProgrammesFragment : Fragment() {

    private var _binding: FragmentProgrammesBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }

    private var allProgrammes: List<Programme> = emptyList()
    private var selectedTab = 0
    private lateinit var adapter: ProgrammeAdapter

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentProgrammesBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        adapter = ProgrammeAdapter()
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
            app.repository.getAllProgrammes().collect { programmes ->
                allProgrammes = programmes
                applyFilter()
            }
        }
    }

    private fun applyFilter() {
        val filtered = when (selectedTab) {
            1 -> allProgrammes.filter { it.status == ProgrammeStatus.ONGOING }
            2 -> allProgrammes.filter { it.status == ProgrammeStatus.UPCOMING }
            3 -> allProgrammes.filter { it.status == ProgrammeStatus.PAST }
            else -> allProgrammes
        }
        binding.tvEmpty.visibility = if (filtered.isEmpty()) View.VISIBLE else View.GONE
        adapter.submitList(filtered)
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
