<footer class="bg-dark text-light pt-5 pb-4 mt-5">
  <div class="container">
    <div class="row gy-4">
      <div class="col-md-4">
        <h5 class="fw-bold"><span class="brand-mark">MWC</span> Maretlagadi Welfare Centre</h5>
        <p class="text-secondary small mt-2">Serving our community since 2023.</p>
      </div>
      <div class="col-md-4">
        <h6 class="fw-semibold">Quick Links</h6>
        <ul class="list-unstyled small">
          <li><a href="about.php" class="footer-link">About Us</a></li>
          <li><a href="programmes.php" class="footer-link">Our Programmes</a></li>
          <li><a href="volunteer.php" class="footer-link">Volunteer</a></li>
          <li><a href="donate.php" class="footer-link">Donate</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <h6 class="fw-semibold">Contact Information</h6>
        <ul class="list-unstyled small text-secondary">
          <li>Email: info@maretlagadiwelfarecentre.co.za</li>
          <li>Phone: +27 (0)82 327 0967</li>
          <li>Address: 6RQR+VR, Ngwanamatlang Village, Jane Furse, 1085</li>
        </ul>
      </div>
    </div>
    <hr class="border-secondary mt-4">
    <p class="text-center small text-secondary mb-0">&copy; <?php echo date('Y'); ?> Maretlagadi Welfare Centre. All rights reserved.</p>
  </div>
</footer>
<!-- Global accessibility controls -->
<div class="mwc-accessibility-bar" aria-label="Accessibility controls">
  <button type="button" class="mwc-floating-btn" data-theme-toggle title="Switch light/dark mode" aria-label="Switch light/dark mode">
    <span class="theme-icon" aria-hidden="true">☾</span>
    <span class="mwc-sr-only theme-label">Dark mode</span>
  </button>
  <button type="button" class="mwc-floating-btn" data-listen-toggle title="Listen to what Maretlagadi is about and how to donate" aria-label="Listen to Maretlagadi information" aria-pressed="false">
    <span aria-hidden="true">🔊</span>
  </button>
  <button type="button" class="mwc-floating-btn" data-listen-stop title="Stop voice" aria-label="Stop voice playback">
    <span aria-hidden="true">■</span>
  </button>
</div>

<!-- Maretlagadi chatbot -->
<button id="mwcChatOpen" class="mwc-chat-open" type="button" aria-label="Open Maretlagadi chatbot" title="Ask Maretlagadi Assistant">💬</button>
<div id="mwcChatPanel" class="mwc-chat-panel" role="dialog" aria-label="Maretlagadi Assistant" aria-hidden="true">
  <div class="mwc-chat-header">
    <div>
      <p class="mwc-chat-title">Maretlagadi Assistant</p>
      <p class="mwc-chat-subtitle">Ask about our centre, programmes and donations</p>
    </div>
    <button id="mwcChatClose" class="mwc-chat-close" type="button" aria-label="Close chatbot">×</button>
  </div>
  <div id="mwcChatMessages" class="mwc-chat-messages" aria-live="polite">
    <div class="mwc-chat-message bot">Hello! I’m the Maretlagadi Assistant. How can I help you today?</div>
  </div>
  <div class="mwc-chat-quick" aria-label="Suggested questions">
    <button type="button" data-chat-question="What is Maretlagadi Welfare Centre?">About us</button>
    <button type="button" data-chat-question="What programmes do you offer?">Programmes</button>
    <button type="button" data-chat-question="How can I donate?">Donate</button>
    <button type="button" data-chat-question="How can I volunteer?">Volunteer</button>
  </div>
  <form id="mwcChatForm" class="mwc-chat-form">
    <label class="mwc-sr-only" for="mwcChatInput">Type your question</label>
    <input id="mwcChatInput" class="mwc-chat-input" type="text" autocomplete="off" placeholder="Ask a question..." maxlength="300">
    <button class="mwc-chat-send" type="submit">Send</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>