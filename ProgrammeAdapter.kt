package com.maretlagadi.welfarecentre.ui.common

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.maretlagadi.welfarecentre.databinding.ItemTwoLineActionableBinding

/** List adapter with a delete action, used by the admin management screens. */
class TwoLineActionableAdapter(
    private val onDelete: (TwoLineItem) -> Unit
) : ListAdapter<TwoLineItem, TwoLineActionableAdapter.ViewHolder>(DIFF) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemTwoLineActionableBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemTwoLineActionableBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: TwoLineItem) {
            binding.tvTitle.text = item.title
            binding.tvSubtitle.text = item.subtitle
            binding.btnDelete.setOnClickListener { onDelete(item) }
        }
    }

    companion object {
        private val DIFF = object : DiffUtil.ItemCallback<TwoLineItem>() {
            override fun areItemsTheSame(oldItem: TwoLineItem, newItem: TwoLineItem) = oldItem.id == newItem.id
            override fun areContentsTheSame(oldItem: TwoLineItem, newItem: TwoLineItem) = oldItem == newItem
        }
    }
}
