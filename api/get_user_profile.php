package lb.edu.ul.bikhedemtak.api;

import android.content.Context;
import com.android.volley.*;
import com.android.volley.toolbox.JsonArrayRequest;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.HashMap;
import java.util.Map;

public class ApiRequest {
    private static final String BASE_URL = "http://10.0.2.2:80/bikhedmtak_mobile_api/api/";
    private static final int TIMEOUT_MS = 30000; // 30 seconds timeout
    private static final int MAX_RETRIES = 1;
    private static final float BACKOFF_MULTIPLIER = 1.0f;

    private static RequestQueue requestQueue;
    private static ApiRequest instance;

    private ApiRequest() {}

    public static synchronized ApiRequest getInstance() {
        if (instance == null) {
            instance = new ApiRequest();
        }
        return instance;
    }

    private synchronized RequestQueue getRequestQueue(Context context) {
        if (requestQueue == null) {
            requestQueue = Volley.newRequestQueue(context.getApplicationContext());
        }
        return requestQueue;
    }

    public void makeGetArrayRequest(Context context, String endpoint, ResponseListener<JSONArray> listener) {
        String url = BASE_URL + endpoint;
        JsonArrayRequest request = new JsonArrayRequest(Request.Method.GET, url, null,
                listener::onSuccess,
                error -> handleError(error, listener));
        configureRequest(request);
        getRequestQueue(context).add(request);
    }

    public void makeGetObjectRequest(Context context, String endpoint, ResponseListener<JSONObject> listener) {
        String url = BASE_URL + endpoint;
        JsonObjectRequest request = new JsonObjectRequest(Request.Method.GET, url, null,
                listener::onSuccess,
                error -> handleError(error, listener));
        configureRequest(request);
        getRequestQueue(context).add(request);
    }

    public void makePostRequest(Context context, String endpoint, JSONObject params, ResponseListener<JSONObject> listener) {
        String url = BASE_URL + endpoint;
        JsonObjectRequest request = new JsonObjectRequest(Request.Method.POST, url, params,
                listener::onSuccess,
                error -> handleError(error, listener)) {
            @Override
            public Map<String, String> getHeaders() {
                return createHeaders();
            }
        };
        configureRequest(request);
        getRequestQueue(context).add(request);
    }

    private void configureRequest(Request<?> request) {
        RetryPolicy policy = new DefaultRetryPolicy(TIMEOUT_MS, MAX_RETRIES, BACKOFF_MULTIPLIER);
        request.setRetryPolicy(policy);
    }

    private Map<String, String> createHeaders() {
        Map<String, String> headers = new HashMap<>();
        headers.put("Content-Type", "application/json");
        return headers;
    }

    private void handleError(VolleyError error, ResponseListener<?> listener) {
        String errorMessage;
        if (error instanceof NetworkError) {
            errorMessage = "Network error. Check your connection.";
        } else if (error instanceof ServerError) {
            errorMessage = "Server error. Try again later.";
        } else if (error instanceof AuthFailureError) {
            errorMessage = "Authentication failed. Please login again.";
        } else if (error instanceof TimeoutError) {
            errorMessage = "Request timed out. Try again.";
        } else {
            errorMessage = "Unexpected error: " + (error.getMessage() != null ? error.getMessage() : "Unknown");
        }
        listener.onFailure(errorMessage);
    }

    public interface ResponseListener<T> {
        void onSuccess(T response);
        void onFailure(String error);
    }
}package lb.edu.ul.bikhedemtak;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;
import androidx.fragment.app.Fragment;
import org.json.JSONArray;
import org.json.JSONObject;

public class FavoriteTaskerFragment extends Fragment {
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_favorite_tasker, container, false);
        LinearLayout taskerContainer = view.findViewById(R.id.taskerContainer);
        String userId = "1"; // Get from session

        ApiRequest.getFavoriteTaskers(getContext(), userId, new ApiRequest.ResponseListener() {
            @Override
            public void onSuccess(String response) {
                try {
                    JSONArray jsonArray = new JSONArray(response);
                    for (int i = 0; i < jsonArray.length(); i++) {
                        JSONObject tasker = jsonArray.getJSONObject(i);
                        TextView textView = new TextView(getContext());
                        textView.setText(tasker.getString("name"));
                        taskerContainer.addView(textView);
                    }
                } catch (Exception e) { e.printStackTrace(); }
            }

            @Override
            public void onFailure(String error) { }
        });

        return view;
    }
}package lb.edu.ul.bikhedemtak;

import android.os.Bundle;
import androidx.appcompat.app.AppCompatActivity;
import androidx.viewpager.widget.ViewPager;
import com.google.android.material.tabs.TabLayout;

public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        // Set up ViewPager and TabLayout
        SectionsPagerAdapter sectionsPagerAdapter = new SectionsPagerAdapter(getSupportFragmentManager());
        ViewPager viewPager = findViewById(R.id.view_pager);
        viewPager.setAdapter(sectionsPagerAdapter);

        TabLayout tabs = findViewById(R.id.tab_layout);
        tabs.setupWithViewPager(viewPager);
    }
}package lb.edu.ul.bikhedemtak;

import android.os.Bundle;
import androidx.fragment.app.Fragment;
import androidx.viewpager.widget.ViewPager;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;

public class MyTaskerFragment extends Fragment {

    public MyTaskerFragment() {
        // Required empty public constructor
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_my_tasker, container, false);

        Button btnFavoriteTasker = view.findViewById(R.id.btnFavoriteTasker);
        Button btnPastTasker = view.findViewById(R.id.btnPastTasker);

        btnFavoriteTasker.setOnClickListener(v -> {
            // Navigate to FavoriteTaskerFragment using ViewPager
            if (getActivity() instanceof MainActivity) {
                MainActivity activity = (MainActivity) getActivity();
                ViewPager viewPager = activity.findViewById(R.id.view_pager);
                viewPager.setCurrentItem(1); // Assuming FavoriteTaskerFragment is the second item
            }
        });

        btnPastTasker.setOnClickListener(v -> {
            // Navigate to PastTaskerFragment using ViewPager
            if (getActivity() instanceof MainActivity) {
                MainActivity activity = (MainActivity) getActivity();
                ViewPager viewPager = activity.findViewById(R.id.view_pager);
                viewPager.setCurrentItem(2); // Assuming PastTaskerFragment is the third item
            }
        });

        return view;
    }
}package lb.edu.ul.bikhedemtak;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import androidx.fragment.app.Fragment;

public class PastTaskerFragment extends Fragment {

    public PastTaskerFragment() {
        // Required empty public constructor
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_past_tasker, container, false);

        // Mock data (replace with database fetch later)
        String taskerName = "John Doe";
        int taskerRating = 4; // Example rating out of 5
        String taskerJobs = "3 overall jobs";
        int taskerPhoto = R.drawable.img; // Replace with your image resource

        // Set tasker photo
        ImageView taskerPhotoView = view.findViewById(R.id.imageTasker);
        taskerPhotoView.setImageResource(taskerPhoto);

        // Set tasker name
        TextView taskerNameView = view.findViewById(R.id.taskerName);
        taskerNameView.setText(taskerName);

        // Set tasker jobs
        TextView taskerJobsView = view.findViewById(R.id.taskerJobs);
        taskerJobsView.setText(taskerJobs);

        // Dynamically add stars based on rating
        LinearLayout ratingContainer = view.findViewById(R.id.ratingContainer);
        for (int i = 0; i < 5; i++) {
            ImageView star = new ImageView(getContext());
            LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.WRAP_CONTENT,
                    LinearLayout.LayoutParams.WRAP_CONTENT
            );
            params.setMargins(4, 0, 4, 0); // Adjust margins as needed
            star.setLayoutParams(params);

            if (i < taskerRating) {
                star.setImageResource(R.drawable.ic_star_empty); // Filled star
            } else {
                star.setImageResource(R.drawable.ic_star_empty); // Empty star
            }

            ratingContainer.addView(star);
        }

        // Handle button clicks
        Button btnChat = view.findViewById(R.id.btnChat);
        Button btnBook = view.findViewById(R.id.btnBook);

        btnChat.setOnClickListener(v -> {
            // Handle chat button click
        });

        btnBook.setOnClickListener(v -> {
            // Handle book button click
        });

        return view;
    }
}package lb.edu.ul.bikhedemtak;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentPagerAdapter;

public class SectionsPagerAdapter extends FragmentPagerAdapter {

    public SectionsPagerAdapter(@NonNull FragmentManager fm) {
        super(fm, BEHAVIOR_RESUME_ONLY_CURRENT_FRAGMENT);
    }

    @NonNull
    @Override
    public Fragment getItem(int position) {
        switch (position) {
            case 0:
                return new FavoriteTaskerFragment();
            case 1:
                return new PastTaskerFragment();
            default:
                return null;
        }
    }

    @Override
    public int getCount() {
        return 2; // Number of fragments (Favorite and Past Tasker)
    }

    @Override
    public CharSequence getPageTitle(int position) {
        switch (position) {
            case 0:
                return "Favorite Tasker";
            case 1:
                return "Past Tasker";
            default:
                return null;
        }
    }
}package lb.edu.ul.bikhedemtak;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import android.widget.Toast;
import androidx.fragment.app.Fragment;
import org.json.JSONException;
import org.json.JSONObject;

public class UserProfileFragment extends Fragment {

    private TextView txtUserName, txtUserEmail;
    private String userId = "1"; // Replace with actual user ID from session

    public UserProfileFragment() {
        // Required empty public constructor
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_user_profile, container, false);

        // Initialize views
        txtUserName = view.findViewById(R.id.txtUserName);
        txtUserEmail = view.findViewById(R.id.txtUserEmail);

        // Fetch user profile data
        fetchUserProfile();

        // Handle logout button click
        view.findViewById(R.id.txtLogout).setOnClickListener(v -> {
            // Implement logout logic here
            Toast.makeText(getContext(), "Logged out", Toast.LENGTH_SHORT).show();
        });

        // Handle "Buy a Gift Card" button click
        view.findViewById(R.id.btnGiftCard).setOnClickListener(v -> {
            Toast.makeText(getContext(), "Buy a Gift Card", Toast.LENGTH_SHORT).show();
        });

        // Handle "Become a Tasker" button click
        view.findViewById(R.id.txtBecomeTasker).setOnClickListener(v -> {
            Toast.makeText(getContext(), "Become a Tasker", Toast.LENGTH_SHORT).show();
        });

        return view;
    }

    // Fetch user profile data from the server
    private void fetchUserProfile() {
        ApiRequest.getUserProfile(getContext(), userId, new ApiRequest.ResponseListener() {
            @Override
            public void onSuccess(String response) {
                try {
                    JSONObject jsonObject = new JSONObject(response);
                    String name = jsonObject.getString("name");
                    String email = jsonObject.getString("email");

                    // Update UI with fetched data
                    txtUserName.setText(name);
                    txtUserEmail.setText(email);
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }

            @Override
            public void onFailure(String error) {
                Toast.makeText(getContext(), "Failed to fetch profile: " + error, Toast.LENGTH_SHORT).show();
            }
        });
    }

    // Update user profile data on the server
    private void updateUserProfile(String name, String email) {
        ApiRequest.updateUserProfile(getContext(), userId, name, email, new ApiRequest.ResponseListener() {
            @Override
            public void onSuccess(String response) {
                Toast.makeText(getContext(), "Profile updated successfully", Toast.LENGTH_SHORT).show();
            }

            @Override
            public void onFailure(String error) {
                Toast.makeText(getContext(), "Failed to update profile: " + error, Toast.LENGTH_SHORT).show();
            }
        });
    }
}
activity_main.xml <?xml version="1.0" encoding="utf-8"?>
<RelativeLayout xmlns:android="http://schemas.android.com/apk/res/android"
    xmlns:tools="http://schemas.android.com/tools"
    android:layout_width="match_parent"
    android:layout_height="match_parent"
    xmlns:app="http://schemas.android.com/apk/res-auto"
    tools:context=".MainActivity">

    <!-- TabLayoactut for navigation between Favorite and Past Tasker -->
    <com.google.android.material.tabs.TabLayout
        android:id="@+id/tab_layout"
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:background="@color/white"
        app:tabTextColor="@color/light_gray"
        app:tabSelectedTextColor="@color/light_gray"
        app:tabIndicatorColor="@color/purple_500" />

    <!-- ViewPager for fragments -->
    <androidx.viewpager.widget.ViewPager
        android:id="@+id/view_pager"
        android:layout_width="match_parent"
        android:layout_height="match_parent"
        android:layout_below="@id/tab_layout" />

</RelativeLayout> fragment_favorite_tasker.xml <?xml version="1.0" encoding="utf-8"?>
<LinearLayout xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent"
    android:layout_height="wrap_content"
    android:orientation="horizontal"
    android:padding="16dp"
    android:background="@color/white">

    <!-- ImageView for Tasker Image -->
    <ImageView
        android:id="@+id/imageTasker"
        android:layout_width="100dp"
        android:layout_height="100dp"
        android:scaleType="centerCrop"
        android:src="@drawable/img"
        android:background="@drawable/circle_background"
        android:clipToOutline="true"/>

    <!-- Container for Name, Rating, and Three Dots -->
    <LinearLayout
        android:id="@+id/taskerContainer"
        android:layout_width="0dp"
        android:layout_height="wrap_content"
        android:layout_weight="1"
        android:orientation="vertical"
        android:paddingStart="16dp">

        <!-- Tasker Name and Three Dots -->
        <RelativeLayout
            android:layout_width="match_parent"
            android:layout_height="wrap_content">

            <!-- Tasker Name -->
            <TextView
                android:id="@+id/taskerName"
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:text="Dianne Russell"
                android:textSize="24sp"
                android:textStyle="bold"
                android:textColor="@color/black" />

            <!-- Three Dots Menu -->
            <ImageView
                android:id="@+id/btnMore"
                android:layout_width="24dp"
                android:layout_height="24dp"
                android:layout_alignParentEnd="true"
                android:src="@drawable/ic_more_vert"
                android:contentDescription="@string/more_options"
                android:padding="4dp" />
        </RelativeLayout>

        <!-- Tasker Rating -->
        <LinearLayout
            android:id="@+id/ratingContainer"
            android:layout_width="wrap_content"
            android:layout_height="wrap_content"
            android:layout_marginTop="8dp"
            android:orientation="horizontal">

            <!-- Stars will be added dynamically here -->
        </LinearLayout>

        <!-- Tasker Jobs -->
        <TextView
            android:id="@+id/taskerJobs"
            android:layout_width="wrap_content"
            android:layout_height="wrap_content"
            android:text="1 overall job"
            android:textSize="16sp"
            android:layout_marginTop="8dp"
            android:textColor="@color/soft_pink" />
    </LinearLayout>

</LinearLayout>fragment_my_tasker.xml<?xml version="1.0" encoding="utf-8"?>
<LinearLayout xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent"
    android:layout_height="match_parent"
    android:orientation="vertical"
    android:padding="16dp"
    android:background="@color/white">

    <TextView
        android:id="@+id/title"
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:text="My Tasker"
        android:textSize="24sp"
        android:textStyle="bold"
        android:textColor="@color/white" />

    <TextView
        android:id="@+id/taskerName"
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:text="Dianne Russell"
        android:textSize="18sp"
        android:layout_marginTop="16dp"
        android:textColor="@color/light_pink" />

    <TextView
        android:id="@+id/taskerRating"
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:text="Rating: 5.0"
        android:textSize="16sp"
        android:layout_marginTop="8dp"
        android:textColor="@color/sky_blue" />

    <TextView
        android:id="@+id/taskerJobs"
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:text="1 overall job"
        android:textSize="16sp"
        android:layout_marginTop="8dp"
        android:textColor="@color/soft_pink" />

    <Button
        android:id="@+id/btnFavoriteTasker"
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:text="Favorite Tasker"
        android:layout_marginTop="16dp"
        android:backgroundTint="@color/purple_500"
        android:textColor="@color/white" />

    <Button
        android:id="@+id/btnPastTasker"
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:text="Past Tasker"
        android:layout_marginTop="16dp"
        android:backgroundTint="@color/purple_700"
        android:textColor="@color/white" />

</LinearLayout>fragment_past_tasker.xml <?xml version="1.0" encoding="utf-8"?>
<LinearLayout xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent"
    android:layout_height="wrap_content"
    android:orientation="vertical"
    android:padding="16dp"
    android:background="@drawable/layout_border">

    <!-- Container for Photo and Info -->
    <LinearLayout
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:orientation="horizontal">

        <!-- Tasker Photo -->
        <ImageView
            android:id="@+id/imageTasker"
            android:layout_width="100dp"
            android:layout_height="100dp"
            android:scaleType="centerCrop"
            android:src="@drawable/img"
            android:background="@drawable/circle_background"
            android:clipToOutline="true"/> <!-- Replace with your image resource -->

        <!-- Container for Tasker Info -->
        <LinearLayout
            android:layout_width="0dp"
            android:layout_height="wrap_content"
            android:layout_weight="1"
            android:orientation="vertical"
            android:paddingStart="16dp">

            <!-- Tasker Name -->
            <TextView
                android:id="@+id/taskerName"
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:text="Dianne Russell"
                android:textSize="24sp"
                android:textStyle="bold"
                android:textColor="@color/black" />

            <!-- Tasker Rating -->
            <LinearLayout
                android:id="@+id/ratingContainer"
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:layout_marginTop="8dp"
                android:orientation="horizontal">

                <!-- Stars will be added dynamically here -->
            </LinearLayout>

            <!-- Tasker Jobs -->
            <TextView
                android:id="@+id/taskerJobs"
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:text="1 overall job"
                android:textSize="16sp"
                android:layout_marginTop="8dp"
                android:textColor="@color/light_gray" />
        </LinearLayout>
    </LinearLayout>

    <!-- Buttons for Past Tasker -->
    <LinearLayout
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:orientation="horizontal"
        android:layout_marginTop="16dp"
        android:weightSum="2">

        <Button
            android:id="@+id/btnChat"
            android:layout_width="0dp"
            android:layout_height="wrap_content"
            android:layout_weight="1"
            android:text="Chat"

            android:backgroundTint="@color/white"
            android:textColor="@color/purple_700"
            android:layout_marginEnd="8dp" />

        <Button
            android:id="@+id/btnBook"
            android:layout_width="0dp"
            android:layout_height="wrap_content"
            android:layout_weight="1"
            android:text="Book"
            android:backgroundTint="@color/white"
            android:textColor="@color/purple_500" />
    </LinearLayout>

</LinearLayout>fragment_user_profile.xml <ScrollView xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent"
    android:layout_height="match_parent"
    android:background="@color/white">

    <LinearLayout
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:orientation="vertical"
        android:padding="16dp">

        <!-- Header: Profile & Logout -->
        <RelativeLayout
            android:layout_width="match_parent"
            android:layout_height="wrap_content"
            android:paddingBottom="16dp">

            <TextView
                android:id="@+id/txtProfileTitle"
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:text="Profile"
                android:textSize="20sp"
                android:textStyle="bold"
                android:textColor="@color/black" />

            <TextView
                android:id="@+id/txtLogout"
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:text="Log out"
                android:textColor="@color/light_gray"
                android:textSize="14sp"
                android:layout_alignParentEnd="true"
                android:clickable="true"
                android:focusable="true" />
        </RelativeLayout>

        <!-- User Profile Picture & Name -->
        <LinearLayout
            android:layout_width="match_parent"
            android:layout_height="wrap_content"
            android:gravity="center"
            android:orientation="vertical"
            android:paddingBottom="16dp">

            <ImageView
                android:id="@+id/imgProfilePicture"
                android:layout_width="80dp"
                android:layout_height="80dp"
                android:scaleType="centerCrop"
                android:src="@drawable/img"
                android:background="@drawable/circle_background" />

            <TextView
                android:id="@+id/txtUserName"
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:text="Brooklyn Simmons"
                android:textSize="18sp"
                android:textStyle="bold"
                android:paddingTop="8dp"
                android:textColor="@color/black" />
        </LinearLayout>

        <!-- Account Info -->
        <LinearLayout
            android:layout_width="match_parent"
            android:layout_height="wrap_content"
            android:orientation="horizontal"
            android:paddingBottom="12dp">

            <TextView
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:text="Account"
                android:textSize="16sp"
                android:textColor="@color/light_gray"
                android:layout_weight="1"/>

            <TextView
                android:id="@+id/txtUserEmail"
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:text="brooklyn.simmons@email.com"
                android:textSize="14sp"
                android:textColor="@color/light_gray" />
        </LinearLayout>

        <View android:layout_width="match_parent" android:layout_height="1dp" android:background="@color/light_gray"/>

        <!-- List of Settings -->
        <LinearLayout
            android:layout_width="match_parent"
            android:layout_height="wrap_content"
            android:orientation="vertical">

            <!-- Change Password -->
            <include layout="@layout/item_profile_option" />

            <!-- Payment -->
            <include layout="@layout/item_profile_option" />

            <!-- Promos -->
            <include layout="@layout/item_profile_option" />

            <!-- Notification -->
            <include layout="@layout/item_profile_option" />

            <!-- Support -->
            <include layout="@layout/item_profile_option" />

        </LinearLayout>

        <!-- Bottom Buttons -->
        <LinearLayout
            android:layout_width="match_parent"
            android:layout_height="wrap_content"
            android:orientation="vertical"
            android:gravity="center"
            android:paddingTop="16dp">

            <!-- Buy a Gift Card -->
            <Button
                android:id="@+id/btnGiftCard"
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:text="🎁 Buy a Gift Card"
                android:textColor="@color/purple_500"
                android:background="?android:attr/selectableItemBackground" />

            <!-- Become a Tasker -->
            <TextView
                android:id="@+id/txtBecomeTasker"
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:text="Become a tasker"
                android:textSize="14sp"
                android:textColor="@color/purple_500"
                android:paddingTop="8dp"
                android:clickable="true"
                android:focusable="true"/>
        </LinearLayout>

    </LinearLayout>
</ScrollView> item_profile_option.xml <?xml version="1.0" encoding="utf-8"?>
<LinearLayout xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent"
    android:layout_height="wrap_content"
    android:paddingVertical="12dp"
    android:orientation="horizontal"
    android:clickable="true"
    android:focusable="true"
    android:paddingHorizontal="8dp">

    <TextView
        android:id="@+id/txtOptionTitle"
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:text="Option Title"
        android:textSize="16sp"
        android:textColor="@color/light_gray"
        android:layout_weight="1" />

    <ImageView
        android:layout_width="20dp"
        android:layout_height="20dp"
        android:src="@drawable/ic_arrow_right" />
</LinearLayout> db_connect.php 
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bikhedemtak_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>get_favorite_taskers.php <?php
require 'db_connect.php';

$user_id = $_GET['user_id'];

$sql = "SELECT taskers.* FROM favorite_taskers 
        JOIN taskers ON favorite_taskers.tasker_id = taskers.id
        WHERE favorite_taskers.user_id = '$user_id'";

$result = $conn->query($sql);

$taskers = [];
while ($row = $result->fetch_assoc()) {
    $taskers[] = $row;
}

echo json_encode($taskers);
$conn->close();
?> get_past_taskers.php <?php
require 'db_connect.php';

$user_id = $_GET['user_id'];

$sql = "SELECT taskers.*, past_taskers.completed_jobs FROM past_taskers
        JOIN taskers ON past_taskers.tasker_id = taskers.id
        WHERE past_taskers.user_id = '$user_id'";

$result = $conn->query($sql);

$taskers = [];
while ($row = $result->fetch_assoc()) {
    $taskers[] = $row;
}

echo json_encode($taskers);
$conn->close();
?> user_profile.php <?php
require 'db_connect.php';

$user_id = $_GET['user_id'];

$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode($user);
} else {
    echo json_encode(["error" => "User not found"]);
}

$stmt->close();
$conn->close();
?>update_profile.php<?php
require 'db_connect.php';

$user_id = $_POST['user_id'];
$name = $_POST['name'];
$email = $_POST['email'];

$stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
$stmt->bind_param("sss", $name, $email, $user_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update profile"]);
}

$stmt->close();
$conn->close();
?>change_password.php<?php
require 'db_connect.php';

$user_id = $_POST['user_id'];
$current_password = $_POST['current_password'];
$new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

// Verify current password
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($current_password, $user['password'])) {
        // Update password
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("ss", $new_password, $user_id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Password updated successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update password"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Current password is incorrect"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}

$stmt->close();
$conn->close();
?>

fix all isseus and gimme the full code with the ful path after update make sure everything fetched dynamically from the database  and work smoothly
