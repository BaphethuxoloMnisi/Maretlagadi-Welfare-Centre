package com.maretlagadi.welfarecentre.ui.admin

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.maretlagadi.welfarecentre.data.entities.ActivityLog
import com.maretlagadi.welfarecentre.databinding.ItemActivityBinding
import com.maretlagadi.welfarecentre.ui.common.styleFor
import com.maretlagadi.welfarecentre.ui.common.timeAgo

class AdminActivityAdapter : ListAdapter<ActivityLog, AdminActivityAdapter.ViewHolder>(DIFF) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemActivityBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    class ViewHolder(private val binding: ItemActivityBinding) : RecyclerView.ViewHolder(binding.root) {
        fun bind(item: ActivityLog) {
            val context = binding.root.context
            val style = styleFor(item.type)
            binding.iconChip.setCardBackgroundColor(ContextCompat.getColor(context, style.bgColorRes))
            binding.ivIcon.setImageResource(style.iconRes)
            binding.ivIcon.imageTintList = ContextCompat.getColorStateList(context, style.fgColorRes)
            binding.tvTitle.text = item.userName
            binding.tvSubtitle.text = item.description
            binding.tvEmail.text = item.userEmail
            binding.tvTime.text = timeAgo(item.timestamp)
        }
    }

    companion object {
        private val DIFF = object : DiffUtil.ItemCallback<ActivityLog>() {
            override fun areItemsTheSame(oldItem: ActivityLog, newItem: ActivityLog) = oldItem.logId == newItem.logId
            override fun areContentsTheSame(oldItem: ActivityLog, newItem: ActivityLog) = oldItem == newItem
        }
    }
}
