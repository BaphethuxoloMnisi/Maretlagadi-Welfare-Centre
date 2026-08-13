package com.maretlagadi.welfarecentre.ui.volunteer

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import com.maretlagadi.welfarecentre.R
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.data.entities.ShiftStatus
import com.maretlagadi.welfarecentre.databinding.FragmentVolunteerBinding
import com.maretlagadi.welfarecentre.utils.InputValidator
import kotlinx.coroutines.flow.combine
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Locale

/**
 * Volunteer dashboard (wireframe 9.4): profile summary, upcoming shifts
 * with accept/decline, and "My Activity" stats. Shows one of three states
 * depending on session: logged out, logged in but not yet a volunteer, or
 * an existing volunteer with the full dashboard.
 */
class VolunteerFragment : Fragment() {

    private var _binding: FragmentVolunteerBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }
    private val monthFormat = SimpleDateFormat("MMM yyyy", Locale.getDefault())
    private val eventDateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentVolunteerBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        binding.btnGoToLogin.setOnClickListener {
            findNavController().navigate(R.id.loginFragment)
        }
        binding.btnSubmitVolunteer.setOnClickListener { submitVolunteerRegistration() }

        refreshState()
    }

    private fun refreshState() {
        val session = app.sessionManager
        if (!session.isLoggedIn()) {
            showOnly(binding.groupLoginPrompt)
            return
        }

        viewLifecycleOwner.lifecycleScope.launch {
            val volunteer = app.repository.getVolunteerForUser(session.getUserId())
            val user = app.repository.getUser(session.getUserId())

            if (volunteer == null) {
                showOnly(binding.groupRegisterVolunteer)
                return@launch
            }

            showOnly(binding.groupVolunteerDashboard)
            binding.tvVolunteerName.text = user?.name ?: "Volunteer"
            binding.tvVolunteerSince.text = "Volunteer since ${monthFormat.format(volunteer.dateRegistered)}"
            binding.tvVolunteerHours.text = "${volunteer.totalHours} hours contributed"
            binding.tvTotalHours.text = "${volunteer.totalHours} hrs"
            binding.tvCertificates.text = volunteer.certificates.toString()

            val adapter = ShiftAdapter(
                onAccept = { respondToShift(it, accept = true) },
                onDecline = { respondToShift(it, accept = false) }
            )
            binding.recyclerShifts.layoutManager = LinearLayoutManager(requireContext())
            binding.recyclerShifts.adapter = adapter

            combine(
                app.repository.getShiftsForVolunteer(volunteer.volunteerId),
                app.repository.getAllEvents()
            ) { shifts, events ->
                val eventsById = events.associateBy { it.eventId }
                shifts.map { ShiftUi(it, eventsById[it.eventId]) }
            }.collect { shiftUis ->
                binding.tvNoShifts.visibility = if (shiftUis.isEmpty()) View.VISIBLE else View.GONE
                adapter.submitList(shiftUis)

                val confirmed = shiftUis.count { it.shift.status == ShiftStatus.CONFIRMED || it.shift.status == ShiftStatus.COMPLETED }
                binding.tvEventsAttended.text = confirmed.toString()

                val now = Calendar.getInstance()
                val hoursThisMonth = shiftUis.count { s ->
                    if (s.shift.status != ShiftStatus.CONFIRMED && s.shift.status != ShiftStatus.COMPLETED) return@count false
                    val eventDate = s.event?.date?.let { runCatching { eventDateFormat.parse(it) }.getOrNull() } ?: return@count false
                    val cal = Calendar.getInstance().apply { time = eventDate }
                    cal.get(Calendar.MONTH) == now.get(Calendar.MONTH) && cal.get(Calendar.YEAR) == now.get(Calendar.YEAR)
                } * 4 // assumes a 4-hour shift; the schema doesn't track per-shift duration yet
                binding.tvHoursThisMonth.text = "$hoursThisMonth hrs"
            }
        }
    }

    private fun respondToShift(shiftUi: ShiftUi, accept: Boolean) {
        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.respondToShift(shiftUi.shift, accept)
            Toast.makeText(
                requireContext(),
                if (accept) "Shift accepted" else "Shift declined",
                Toast.LENGTH_SHORT
            ).show()
        }
    }

    private fun submitVolunteerRegistration() {
        binding.tvVolunteerError.visibility = View.GONE
        val skills = binding.etSkills.text.toString().trim()
        val availability = binding.etAvailability.text.toString().trim()

        if (!InputValidator.isNotBlank(skills) || !InputValidator.isNotBlank(availability)) {
            binding.tvVolunteerError.text = "Please tell us your skills and availability."
            binding.tvVolunteerError.visibility = View.VISIBLE
            return
        }

        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.registerVolunteer(app.sessionManager.getUserId(), skills, availability)
            Toast.makeText(requireContext(), "Thank you for registering as a volunteer!", Toast.LENGTH_LONG).show()
            refreshState()
        }
    }

    private fun showOnly(visibleGroup: View) {
        listOf(binding.groupLoginPrompt, binding.groupRegisterVolunteer, binding.groupVolunteerDashboard)
            .forEach { it.visibility = if (it == visibleGroup) View.VISIBLE else View.GONE }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
