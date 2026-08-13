package com.maretlagadi.welfarecentre.ui.splash

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import androidx.navigation.fragment.findNavController
import com.maretlagadi.welfarecentre.R
import com.maretlagadi.welfarecentre.WelfareApp
import com.maretlagadi.welfarecentre.databinding.FragmentSplashBinding
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

/**
 * Brief branded splash screen (wireframe 9.1) that also decides where to
 * route the user next: straight to Home if a session is already saved,
 * otherwise to Login.
 */
class SplashFragment : Fragment() {

    private var _binding: FragmentSplashBinding? = null
    private val binding get() = _binding!!
    private val app: WelfareApp by lazy { requireActivity().application as WelfareApp }

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        _binding = FragmentSplashBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        viewLifecycleOwner.lifecycleScope.launch {
            delay(900)
            if (!isAdded) return@launch
            val destination = if (app.sessionManager.isLoggedIn()) {
                R.id.action_splash_to_home
            } else {
                R.id.action_splash_to_login
            }
            findNavController().navigate(destination)
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
