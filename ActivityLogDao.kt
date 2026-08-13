package com.maretlagadi.welfarecentre.ui.volunteer

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.maretlagadi.welfarecentre.data.entities.ShiftStatus
import com.maretlagadi.welfarecentre.databinding.ItemShiftBinding

class ShiftAdapter(
    private val onAccept: (ShiftUi) -> Unit,
    private val onDecline: (ShiftUi) -> Unit
) : ListAdapter<ShiftUi, ShiftAdapter.ViewHolder>(DIFF) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemShiftBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemShiftBinding) : RecyclerView.ViewHolder(binding.root) {
        fun bind(item: ShiftUi) {
            binding.tvTitle.text = item.event?.name ?: "Event"
            binding.tvSubtitle.text = "${item.event?.date.orEmpty()} · ${item.event?.location.orEmpty()}"

            val isPending = item.shift.status == ShiftStatus.PENDING
            binding.groupActions.visibility = if (isPending) View.VISIBLE else View.GONE
            binding.tvStatus.visibility = if (isPending) View.GONE else View.VISIBLE
            binding.tvStatus.text = when (item.shift.status) {
                ShiftStatus.CONFIRMED -> "Confirmed"
                ShiftStatus.CANCELLED -> "Declined"
                ShiftStatus.COMPLETED -> "Completed"
                ShiftStatus.PENDING -> ""
            }
            binding.tvStatus.setTextColor(
                binding.root.context.getColor(
                    when (item.shift.status) {
                        ShiftStatus.CONFIRMED -> com.maretlagadi.welfarecentre.R.color.success
                        ShiftStatus.CANCELLED -> com.maretlagadi.welfarecentre.R.color.error
                        else -> com.maretlagadi.welfarecentre.R.color.text_secondary
                    }
                )
            )

            binding.btnAccept.setOnClickListener { onAccept(item) }
            binding.btnDecline.setOnClickListener { onDecline(item) }
        }
    }

    companion object {
        private val DIFF = object : DiffUtil.ItemCallback<ShiftUi>() {
            override fun areItemsTheSame(oldItem: ShiftUi, newItem: ShiftUi) = oldItem.shift.shiftId == newItem.shift.shiftId
            override fun areContentsTheSame(oldItem: ShiftUi, newItem: ShiftUi) = oldItem == newItem
        }
    }
}
