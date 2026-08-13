package com.maretlagadi.welfarecentre.ui.notifications

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.maretlagadi.welfarecentre.data.entities.Notification
import com.maretlagadi.welfarecentre.databinding.ItemUpdateBinding
import com.maretlagadi.welfarecentre.ui.common.styleFor
import com.maretlagadi.welfarecentre.ui.common.timeAgo

class NotificationAdapter(
    private val onClick: (Notification) -> Unit
) : ListAdapter<Notification, NotificationAdapter.ViewHolder>(DIFF) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemUpdateBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemUpdateBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: Notification) {
            val context = binding.root.context
            val style = styleFor(item.type)
            binding.iconChip.setCardBackgroundColor(ContextCompat.getColor(context, style.bgColorRes))
            binding.ivIcon.setImageResource(style.iconRes)
            binding.ivIcon.imageTintList = ContextCompat.getColorStateList(context, style.fgColorRes)
            binding.tvTitle.text = item.title
            binding.tvSubtitle.text = item.message
            binding.tvTime.text = timeAgo(item.date)
            binding.unreadDot.visibility = if (item.status == "UNREAD") View.VISIBLE else View.GONE
            binding.root.setOnClickListener { onClick(item) }
        }
    }

    companion object {
        private val DIFF = object : DiffUtil.ItemCallback<Notification>() {
            override fun areItemsTheSame(oldItem: Notification, newItem: Notification) =
                oldItem.notificationId == newItem.notificationId
            override fun areContentsTheSame(oldItem: Notification, newItem: Notification) = oldItem == newItem
        }
    }
}
