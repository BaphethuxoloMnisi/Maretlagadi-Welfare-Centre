package com.maretlagadi.welfarecentre.ui.home

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.navigation.fragment.findNavController
import com.maretlagadi.welfarecentre.R
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.databinding.FragmentHomeBinding
import com.maretlagadi.welfarecentre.databinding.ItemUpdateFlatBinding
import com.maretlagadi.welfarecentre.ui.common.styleFor
import com.maretlagadi.welfarecentre.ui.common.timeAgo
import kotlinx.coroutines.launch

/** Home screen: greeting, quick-access shortcuts and a Latest Updates feed (wireframe 9.2). */
class HomeFragment : Fragment() {

    private var _binding: FragmentHomeBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentHomeBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        val session = app.sessionManager
        if (session.isLoggedIn()) {
            binding.cardGuestBanner.visibility = View.GONE
            viewLifecycleOwner.lifecycleScope.launch {
                val user = app.repository.getUser(session.getUserId())
                val firstName = user?.name?.trim()?.split(" ")?.firstOrNull() ?: "friend"
                binding.tvWelcome.text = "Hello, $firstName \uD83D\uDC4B"
            }
            binding.cardAdminBanner.visibility = if (session.isAdmin()) View.VISIBLE else View.GONE
        } else {
            binding.tvWelcome.text = "Hello \uD83D\uDC4B"
            binding.cardGuestBanner.visibility = View.VISIBLE
            binding.cardAdminBanner.visibility = View.GONE
        }

        binding.cardAdminBanner.setOnClickListener {
            findNavController().navigate(R.id.action_home_to_admin)
        }

        binding.btnNotifications.setOnClickListener {
            findNavController().navigate(R.id.action_home_to_notifications)
        }
        binding.tvViewAllUpdates.setOnClickListener {
            findNavController().navigate(R.id.action_home_to_notifications)
        }

        binding.tileProgrammes.setOnClickListener { findNavController().navigate(R.id.programmesFragment) }
        binding.tileVolunteer.setOnClickListener { findNavController().navigate(R.id.volunteerFragment) }
        binding.tileCalendar.setOnClickListener { findNavController().navigate(R.id.calendarFragment) }
        binding.tileContact.setOnClickListener { findNavController().navigate(R.id.action_home_to_contact) }

        viewLifecycleOwner.lifecycleScope.launch {
            app.repository.getAllNotifications().collect { notifications ->
                binding.updatesContainer.removeAllViews()
                notifications.take(3).forEach { notification ->
                    val itemBinding = ItemUpdateFlatBinding.inflate(
                        LayoutInflater.from(requireContext()), binding.updatesContainer, false
                    )
                    val style = styleFor(notification.type)
                    itemBinding.iconChip.setCardBackgroundColor(ContextCompat.getColor(requireContext(), style.bgColorRes))
                    itemBinding.ivIcon.setImageResource(style.iconRes)
                    itemBinding.ivIcon.imageTintList =
                        ContextCompat.getColorStateList(requireContext(), style.fgColorRes)
                    itemBinding.tvTitle.text = notification.title
                    itemBinding.tvSubtitle.text = notification.message
                    itemBinding.tvTime.text = timeAgo(notification.date)
                    binding.updatesContainer.addView(itemBinding.root)
                }
            }
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
