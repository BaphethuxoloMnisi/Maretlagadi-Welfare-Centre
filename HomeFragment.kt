package com.maretlagadi.welfarecentre.ui.calendarview

import android.graphics.Color
import android.os.Bundle
import android.view.Gravity
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.GridLayout
import android.widget.TextView
import android.widget.Toast
import androidx.core.content.ContextCompat
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.navigation.fragment.findNavController
import com.maretlagadi.welfarecentre.R
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.data.entities.Event
import com.maretlagadi.welfarecentre.databinding.FragmentCalendarBinding
import com.maretlagadi.welfarecentre.databinding.ItemEventDateBinding
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Locale

/**
 * Event Calendar screen (wireframe 9.7): a month grid with a dot on any
 * day that has an event, plus an Upcoming Events list below. Tapping an
 * event lets a registered volunteer sign up for that shift directly from
 * here.
 */
class CalendarFragment : Fragment() {

    private var _binding: FragmentCalendarBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }

    private val displayedMonth = Calendar.getInstance()
    private val eventDateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
    private val monthYearFormat = SimpleDateFormat("MMMM yyyy", Locale.getDefault())
    private val dayFormat = SimpleDateFormat("d", Locale.getDefault())
    private val monthAbbrevFormat = SimpleDateFormat("MMM", Locale.getDefault())

    private var allEvents: List<Event> = emptyList()

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentCalendarBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        buildWeekdayHeader()
        binding.btnPrevMonth.setOnClickListener {
            displayedMonth.add(Calendar.MONTH, -1)
            renderMonthGrid()
        }
        binding.btnNextMonth.setOnClickListener {
            displayedMonth.add(Calendar.MONTH, 1)
            renderMonthGrid()
        }

        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.getAllEvents().collect { events ->
                allEvents = events
                renderMonthGrid()
                renderUpcomingEvents()
            }
        }
    }

    private fun buildWeekdayHeader() {
        binding.weekdayHeader.removeAllViews()
        listOf("Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun").forEach { label ->
            val tv = TextView(requireContext()).apply {
                text = label
                gravity = Gravity.CENTER
                textSize = 11f
                setTextColor(ContextCompat.getColor(requireContext(), R.color.text_muted))
                layoutParams = GridLayout.LayoutParams(
                    GridLayout.spec(GridLayout.UNDEFINED, 1f)
                ).apply { width = 0; height = ViewGroup.LayoutParams.WRAP_CONTENT }
            }
            binding.weekdayHeader.addView(tv)
        }
    }

    private fun renderMonthGrid() {
        binding.tvMonthYear.text = monthYearFormat.format(displayedMonth.time)
        binding.calendarGrid.removeAllViews()

        val eventDatesInMonth = allEvents.mapNotNull { runCatching { eventDateFormat.parse(it.date) }.getOrNull() }
            .filter {
                val c = Calendar.getInstance().apply { time = it }
                c.get(Calendar.MONTH) == displayedMonth.get(Calendar.MONTH) &&
                    c.get(Calendar.YEAR) == displayedMonth.get(Calendar.YEAR)
            }.map { Calendar.getInstance().apply { time = it }.get(Calendar.DAY_OF_MONTH) }
            .toSet()

        val monthStart = displayedMonth.clone() as Calendar
        monthStart.set(Calendar.DAY_OF_MONTH, 1)
        // ISO: Monday = 1 ... Sunday = 7, so the grid always starts on a Monday column.
        val firstWeekday = (monthStart.get(Calendar.DAY_OF_WEEK) + 5) % 7
        val daysInMonth = monthStart.getActualMaximum(Calendar.DAY_OF_MONTH)

        val today = Calendar.getInstance()
        val isCurrentMonth = today.get(Calendar.MONTH) == displayedMonth.get(Calendar.MONTH) &&
            today.get(Calendar.YEAR) == displayedMonth.get(Calendar.YEAR)

        repeat(firstWeekday) {
            binding.calendarGrid.addView(dayCell(null, hasEvent = false, isToday = false))
        }
        for (day in 1..daysInMonth) {
            val isToday = isCurrentMonth && today.get(Calendar.DAY_OF_MONTH) == day
            binding.calendarGrid.addView(dayCell(day, eventDatesInMonth.contains(day), isToday))
        }
    }

    private fun dayCell(day: Int?, hasEvent: Boolean, isToday: Boolean): View {
        val cell = LayoutInflater.from(requireContext())
            .inflate(R.layout.item_calendar_day, binding.calendarGrid, false)
        val tvDay = cell.findViewById<TextView>(R.id.tvDay)
        val dot = cell.findViewById<View>(R.id.eventDot)
        cell.layoutParams = GridLayout.LayoutParams(
            GridLayout.spec(GridLayout.UNDEFINED, 1f)
        ).apply { width = 0; height = ViewGroup.LayoutParams.WRAP_CONTENT }

        if (day == null) {
            tvDay.text = ""
            dot.visibility = View.INVISIBLE
            return cell
        }
        tvDay.text = day.toString()
        dot.visibility = if (hasEvent) View.VISIBLE else View.INVISIBLE
        if (isToday) {
            tvDay.setBackgroundResource(R.drawable.circle_primary)
            tvDay.setTextColor(Color.WHITE)
        } else if (hasEvent) {
            tvDay.setBackgroundResource(R.drawable.circle_primary_light)
            tvDay.setTextColor(ContextCompat.getColor(requireContext(), R.color.brand_primary_dark))
        } else {
            tvDay.background = null
            tvDay.setTextColor(ContextCompat.getColor(requireContext(), R.color.text_primary))
        }
        return cell
    }

    private fun renderUpcomingEvents() {
        binding.eventsContainer.removeAllViews()
        val sorted = allEvents.sortedBy { it.date }
        binding.tvEmpty.visibility = if (sorted.isEmpty()) View.VISIBLE else View.GONE

        sorted.forEach { event ->
            val itemBinding = ItemEventDateBinding.inflate(
                LayoutInflater.from(requireContext()), binding.eventsContainer, false
            )
            val parsedDate = runCatching { eventDateFormat.parse(event.date) }.getOrNull()
            itemBinding.tvDay.text = parsedDate?.let { dayFormat.format(it) } ?: "?"
            itemBinding.tvMonth.text = parsedDate?.let { monthAbbrevFormat.format(it).uppercase() } ?: ""
            itemBinding.tvTitle.text = event.name
            itemBinding.tvSubtitle.text = event.location

            val session = app.sessionManager
            itemBinding.tvAction.visibility = View.VISIBLE
            itemBinding.tvAction.text = if (session.isLoggedIn()) "Sign up" else "Log in to sign up"
            itemBinding.root.setOnClickListener { handleEventTap(event) }

            binding.eventsContainer.addView(itemBinding.root)
        }
    }

    private fun handleEventTap(event: Event) {
        val session = app.sessionManager
        if (!session.isLoggedIn()) {
            findNavController().navigate(R.id.loginFragment)
            return
        }
        viewLifecycleOwner.lifecycleScope.launch {
            val volunteer = app.repository.getVolunteerForUser(session.getUserId())
            if (volunteer == null) {
                Toast.makeText(requireContext(), "Register as a volunteer first to sign up for shifts.", Toast.LENGTH_LONG).show()
                findNavController().navigate(R.id.volunteerFragment)
                return@launch
            }
            app.repository.signUpForShift(volunteer.volunteerId, event.eventId)
            Toast.makeText(requireContext(), "Signed up for ${event.name}", Toast.LENGTH_SHORT).show()
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
