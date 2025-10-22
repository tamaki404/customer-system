





<div class="success-box" style="text-align: center; background-color: #e6f9ec; border: 1px solid #b2e2c4; border-radius: 10px; padding: 20px; width: 100%; max-width: 500px; margin: 30px auto; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);">
    <h2 style="color: #28a745; font-weight: 600; margin-bottom: 10px;">Great job!</h2>
    <p style="color: #333; font-size: 15px; line-height: 1.6;">
        Your updated details have been sent for re-evaluation.<br>
        Please check back later or monitor your notifications for the review result.
    </p>
</div>

                    
            <form action="{{ route('logout') }}" method="POST" style="display:inline; width: auto;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>

