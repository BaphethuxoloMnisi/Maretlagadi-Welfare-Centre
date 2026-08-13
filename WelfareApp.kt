package com.maretlagadi.welfarecentre.ui.common

import com.maretlagadi.welfarecentre.R
import com.maretlagadi.welfarecentre.data.entities.ActivityType

/** Maps an activity log entry's type to the icon + colour chip shown next to it. */
data class ActivityStyle(val iconRes: Int, val bgColorRes: Int, val fgColorRes: Int)

fun styleFor(type: ActivityType): ActivityStyle = when (type) {
    ActivityType.LOGIN -> ActivityStyle(R.drawable.ic_profile, R.color.chip_teal_bg, R.color.chip_teal_fg)
    ActivityType.LOGOUT -> ActivityStyle(R.drawable.ic_profile, R.color.chip_gray_bg, R.color.chip_gray_fg)
    ActivityType.REGISTER -> ActivityStyle(R.drawable.ic_profile, R.color.chip_blue_bg, R.color.chip_blue_fg)
    ActivityType.PASSWORD_RESET, ActivityType.PASSWORD_CHANGED ->
        ActivityStyle(R.drawable.ic_settings, R.color.chip_gray_bg, R.color.chip_gray_fg)
    ActivityType.PROFILE_UPDATED -> ActivityStyle(R.drawable.ic_settings, R.color.chip_blue_bg, R.color.chip_blue_fg)
    ActivityType.VOLUNTEER_REGISTERED -> ActivityStyle(R.drawable.ic_volunteer, R.color.chip_orange_bg, R.color.chip_orange_fg)
    ActivityType.SHIFT_SIGNUP -> ActivityStyle(R.drawable.ic_calendar, R.color.chip_blue_bg, R.color.chip_blue_fg)
    ActivityType.SHIFT_ACCEPTED -> ActivityStyle(R.drawable.ic_calendar, R.color.chip_teal_bg, R.color.chip_teal_fg)
    ActivityType.SHIFT_DECLINED -> ActivityStyle(R.drawable.ic_calendar, R.color.chip_red_bg, R.color.chip_red_fg)
    ActivityType.DONATION_LOGGED -> ActivityStyle(R.drawable.ic_donation, R.color.chip_red_bg, R.color.chip_red_fg)
    ActivityType.ENQUIRY_SUBMITTED -> ActivityStyle(R.drawable.ic_announcement, R.color.chip_purple_bg, R.color.chip_purple_fg)
}
