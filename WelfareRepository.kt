package com.maretlagadi.welfarecentre.ui.common

/** Simple display model shared by list screens (programmes, events, notifications, admin lists). */
data class TwoLineItem(
    val id: Long,
    val title: String,
    val subtitle: String,
    val meta: String? = null
)
