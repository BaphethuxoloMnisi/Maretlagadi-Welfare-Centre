/* =========================================================
   MARETLAGADI ACCESSIBILITY + CHAT ASSISTANT
   - Light / dark theme
   - Text-to-speech "Listen" mode
   - Website chatbot for common questions
   ========================================================= */

(function () {
  'use strict';

  const siteSummary = `Welcome to Maretlagadi Welfare Centre. Maretlagadi Welfare Centre is a non-profit organisation dedicated to supporting vulnerable people in the community, including children with disabilities. The centre supports education, healthcare, social support, skills development, youth mentorship, elderly care, environmental awareness and community outreach. Its mission is to empower and uplift vulnerable community members, and its values include compassion, transparency, inclusivity and sustainable community-driven development. Donations help the centre continue these programmes. To donate, open the Donate page, choose one of the suggested amounts or enter your own amount, provide your email address, and select Proceed to Secure Payment. Payments are processed securely through Paystack in South African rand. You can also get involved by volunteering or by contacting Maretlagadi Welfare Centre using the contact information on this website.`;

  const answers = {
    about: `Maretlagadi Welfare Centre is a non-profit organisation supporting vulnerable members of the community, including children with disabilities, through education, care, social support and outreach programmes.`,
    mission: `Maretlagadi's mission is to empower and uplift vulnerable community members by improving access to education, healthcare and social support.`,
    programmes: `The centre's programmes include Education Support, Skills Development, Community Outreach, Youth Mentorship, Elderly Care Support and Environmental Awareness.`,
    donate: `To donate, open the Donate page, choose R100, R250, R500 or enter another amount, add your email address, and click Proceed to Secure Payment. The website uses Paystack for secure payments in South African rand.`,
    volunteer: `You can get involved through the Volunteer page. Use the Volunteer link in the navigation menu to submit your details and interest in helping the centre.`,
    contact: `You can contact Maretlagadi Welfare Centre using the Contact page. The footer lists info@maretlagadiwelfarecentre.co.za, +27 (0)82 327 0967, and 6RQR+VR, Ngwanamatlang Village, Jane Furse, 1085.`,
    thanks: `You're welcome. I'm here if you'd like to know more about Maretlagadi, its programmes, volunteering or donations.`
  };

  function initTheme() {
    const savedTheme = localStorage.getItem('mwc-theme');
    const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    setTheme(savedTheme || (systemDark ? 'dark' : 'light'));

    document.querySelectorAll('[data-theme-toggle]').forEach(button => {
      button.addEventListener('click', () => {
        const next = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        setTheme(next);
        localStorage.setItem('mwc-theme', next);
      });
    });
  }

  function setTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);
    document.body.classList.toggle('mwc-dark', theme === 'dark');
    document.querySelectorAll('[data-theme-toggle]').forEach(button => {
      const icon = button.querySelector('.theme-icon');
      const text = button.querySelector('.theme-label');
      if (icon) icon.textContent = theme === 'dark' ? '☀' : '☾';
      if (text) text.textContent = theme === 'dark' ? 'Light mode' : 'Dark mode';
      button.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
    });
  }

  function initSpeech() {
    const listenButtons = document.querySelectorAll('[data-listen-toggle]');
    const stopButtons = document.querySelectorAll('[data-listen-stop]');

    listenButtons.forEach(button => {
      button.addEventListener('click', () => {
        if (!('speechSynthesis' in window)) {
          alert('Text-to-speech is not supported by this browser. Please try a recent version of Chrome, Edge, Safari or Firefox.');
          return;
        }

        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(siteSummary);
        utterance.lang = 'en-ZA';
        utterance.rate = 0.95;
        utterance.pitch = 1;

        const voices = window.speechSynthesis.getVoices();
        const preferred = voices.find(v => /en-ZA/i.test(v.lang)) || voices.find(v => /^en/i.test(v.lang));
        if (preferred) utterance.voice = preferred;

        button.classList.add('is-speaking');
        button.setAttribute('aria-pressed', 'true');
        utterance.onend = utterance.onerror = () => {
          button.classList.remove('is-speaking');
          button.setAttribute('aria-pressed', 'false');
        };
        window.speechSynthesis.speak(utterance);
      });
    });

    stopButtons.forEach(button => {
      button.addEventListener('click', () => {
        if ('speechSynthesis' in window) window.speechSynthesis.cancel();
        document.querySelectorAll('[data-listen-toggle]').forEach(b => {
          b.classList.remove('is-speaking');
          b.setAttribute('aria-pressed', 'false');
        });
      });
    });
  }

  function initChatbot() {
    const openBtn = document.getElementById('mwcChatOpen');
    const panel = document.getElementById('mwcChatPanel');
    const closeBtn = document.getElementById('mwcChatClose');
    const form = document.getElementById('mwcChatForm');
    const input = document.getElementById('mwcChatInput');
    const messages = document.getElementById('mwcChatMessages');

    if (!openBtn || !panel || !form || !input || !messages) return;

    const toggle = (show) => {
      panel.classList.toggle('show', show);
      panel.setAttribute('aria-hidden', show ? 'false' : 'true');
      if (show) setTimeout(() => input.focus(), 100);
    };

    openBtn.addEventListener('click', () => toggle(!panel.classList.contains('show')));
    closeBtn.addEventListener('click', () => toggle(false));

    document.querySelectorAll('[data-chat-question]').forEach(button => {
      button.addEventListener('click', () => {
        const question = button.getAttribute('data-chat-question');
        addMessage(question, 'user');
        setTimeout(() => addMessage(getAnswer(question), 'bot'), 250);
      });
    });

    form.addEventListener('submit', (event) => {
      event.preventDefault();
      const question = input.value.trim();
      if (!question) return;
      addMessage(question, 'user');
      input.value = '';
      setTimeout(() => addMessage(getAnswer(question), 'bot'), 250);
    });

    function addMessage(text, who) {
      const bubble = document.createElement('div');
      bubble.className = `mwc-chat-message ${who}`;
      bubble.textContent = text;
      messages.appendChild(bubble);
      messages.scrollTop = messages.scrollHeight;
    }

    function getAnswer(question) {
      const q = question.toLowerCase();
      if (/thank|thanks|thank you/.test(q)) return answers.thanks;
      if (/donat|money|pay|payment|paystack|contribut/.test(q)) return answers.donate;
      if (/volunteer|help out|get involved/.test(q)) return answers.volunteer;
      if (/contact|email|phone|address|where/.test(q)) return answers.contact;
      if (/programme|program|education|skills|youth|elderly|outreach|environment/.test(q)) return answers.programmes;
      if (/mission|vision|value/.test(q)) return answers.mission;
      if (/about|what is|who is|maretlagadi|centre|center/.test(q)) return answers.about;
      if (/hello|hi|hey|good morning|good afternoon|good evening/.test(q)) {
        return `Hello! I'm the Maretlagadi Assistant. You can ask me about the centre, its programmes, how to donate, volunteering or contact details.`;
      }
      return `I can help with questions about Maretlagadi Welfare Centre, its programmes, donations, volunteering and contact information. Try asking “How can I donate?” or “What programmes do you offer?”`;
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    initTheme();
    initSpeech();
    initChatbot();
  });
})();
