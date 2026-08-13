<?xml version="1.0" encoding="utf-8"?>
<com.google.android.material.card.MaterialCardView xmlns:android="http://schemas.android.com/apk/res/android"
    style="@style/ListItemCard">

    <LinearLayout
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:orientation="horizontal"
        android:gravity="center_vertical"
        android:padding="14dp">

        <LinearLayout
            android:layout_width="0dp"
            android:layout_height="wrap_content"
            android:layout_weight="1"
            android:orientation="vertical">

            <TextView
                android:id="@+id/tvTitle"
                android:layout_width="match_parent"
                android:layout_height="wrap_content"
                android:textStyle="bold"
                android:textSize="16sp"
                android:textColor="@color/text_primary" />

            <TextView
                android:id="@+id/tvSubtitle"
                android:layout_width="match_parent"
                android:layout_height="wrap_content"
                android:layout_marginTop="4dp"
                android:textColor="@color/text_secondary" />
        </LinearLayout>

        <ImageButton
            android:id="@+id/btnDelete"
            android:layout_width="wrap_content"
            android:layout_height="wrap_content"
            android:background="?attr/selectableItemBackgroundBorderless"
            android:src="@android:drawable/ic_menu_delete"
            android:contentDescription="@string/delete" />

    </LinearLayout>
</com.google.android.material.card.MaterialCardView>
