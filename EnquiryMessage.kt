package com.maretlagadi.welfarecentre.ui.programmes

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.maretlagadi.welfarecentre.data.entities.Programme
import com.maretlagadi.welfarecentre.databinding.ItemProgrammeBinding

class ProgrammeAdapter : ListAdapter<Programme, ProgrammeAdapter.ViewHolder>(DIFF) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemProgrammeBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    class ViewHolder(private val binding: ItemProgrammeBinding) : RecyclerView.ViewHolder(binding.root) {
        fun bind(item: Programme) {
            binding.tvTitle.text = item.title
            binding.tvSubtitle.text = item.description
            binding.tvMeta.text = item.schedule
        }
    }

    companion object {
        private val DIFF = object : DiffUtil.ItemCallback<Programme>() {
            override fun areItemsTheSame(oldItem: Programme, newItem: Programme) = oldItem.programmeId == newItem.programmeId
            override fun areContentsTheSame(oldItem: Programme, newItem: Programme) = oldItem == newItem
        }
    }
}
