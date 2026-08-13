package com.maretlagadi.welfarecentre.ui.common

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.maretlagadi.welfarecentre.databinding.ItemTwoLineBinding

/** Read-only list adapter used for Programmes, Events and Notifications. */
class TwoLineAdapter(
    private val onClick: ((TwoLineItem) -> Unit)? = null
) : ListAdapter<TwoLineItem, TwoLineAdapter.ViewHolder>(DIFF) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemTwoLineBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemTwoLineBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: TwoLineItem) {
            binding.tvTitle.text = item.title
            binding.tvSubtitle.text = item.subtitle
            if (item.meta != null) {
                binding.tvMeta.text = item.meta
                binding.tvMeta.visibility = View.VISIBLE
            } else {
                binding.tvMeta.visibility = View.GONE
            }
            binding.root.setOnClickListener { onClick?.invoke(item) }
        }
    }

    companion object {
        private val DIFF = object : DiffUtil.ItemCallback<TwoLineItem>() {
            override fun areItemsTheSame(oldItem: TwoLineItem, newItem: TwoLineItem) = oldItem.id == newItem.id
            override fun areContentsTheSame(oldItem: TwoLineItem, newItem: TwoLineItem) = oldItem == newItem
        }
    }
}
