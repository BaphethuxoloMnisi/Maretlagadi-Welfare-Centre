package com.maretlagadi.welfarecentre.ui.common

import com.maretlagadi.welfarecentre.R
import com.maretlagadi.welfarecentre.data.entities.NotificationType
import java.util.concurrent.TimeUnit

/** Maps a notification's type to the icon + colour chip shown next to it. */
data class NotificationStyle(val iconRes: Int, val bgColorRes: Int, val fgColorRes: Int)

fun styleFor(type: NotificationType): NotificationStyle = when (type) {
    NotificationType.REMINDER -> NotificationStyle(R.drawable.ic_reminder, R.color.chip_teal_bg, R.color.chip_teal_fg)
    NotificationType.UPDATE -> NotificationStyle(R.drawable.ic_update, R.color.chip_blue_bg, R.color.chip_blue_fg)
    NotificationType.OPPORTUNITY -> NotificationStyle(R.drawable.ic_opportunity, R.color.chip_purple_bg, R.color.chip_purple_fg)
    NotificationType.ALERT -> NotificationStyle(R.drawable.ic_alert, R.color.chip_red_bg, R.color.chip_red_fg)
    NotificationType.ANNOUNCEMENT -> NotificationStyle(R.drawable.ic_announcement, R.color.chip_orange_bg, R.color.chip_orange_fg)
}

/** Simple "time ago" formatter (e.g. "2h ago", "3d ago") for update/notification timestamps. */
fun timeAgo(timestampMillis: Long): String {
    val diff = System.currentTimeMillis() - timestampMillis
    return when {
        diff < TimeUnit.MINUTES.toMillis(1) -> "Just now"
        diff < TimeUnit.HOURS.toMillis(1) -> "${TimeUnit.MILLISECONDS.toMinutes(diff)}m ago"
        diff < TimeUnit.DAYS.toMillis(1) -> "${TimeUnit.MILLISECONDS.toHours(diff)}h ago"
        else -> "${TimeUnit.MILLISECONDS.toDays(diff)}d ago"
    }
}
