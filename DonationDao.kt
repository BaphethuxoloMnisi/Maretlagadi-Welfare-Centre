package com.maretlagadi.welfarecentre.ui.volunteer

import com.maretlagadi.welfarecentre.data.entities.Event
import com.maretlagadi.welfarecentre.data.entities.ShiftStatus
import com.maretlagadi.welfarecentre.data.entities.VolunteerShift

/** A shift paired with its event details, for display on the Volunteer dashboard. */
data class ShiftUi(val shift: VolunteerShift, val event: Event?)
