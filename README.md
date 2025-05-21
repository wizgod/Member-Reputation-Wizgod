# 👍👎 Like/Dislike Member Reputation for phpBB

**Extension for the phpBB forum software**  
Allows users to like or dislike posts, contributing to each member’s reputation score.

---

## Features

- 👍 Like or 👎 Dislike posts directly
- 🧮 Member reputation is calculated based on received feedback
- 🔐 Permission control for reacting
- 🧰 ACP settings to configure reaction behavior and limits

---

## Installation

1. Clone or download into:  
   `ext/danieltj/memberreputation/`

2. In the phpBB Admin Control Panel:  
   **Customize → Extensions → Enable "Member Reputation"**

---

## Requirements

- phpBB: `>= 3.3.15`
- PHP: `>= 8.2`

---

## How It Works

When a user likes or dislikes a post:
- A reaction is recorded
- The post author’s reputation score is updated
- Users can only react once per post

---

## Future Ideas

- 📈 Reputation leaderboards
- ⏳ Reaction cooldowns or limits
- 💬 Comment on reactions (e.g., why disliked)

---

## Credits

Forked and modernized from github repository https://github.com/Steve-C/Member-Reputation from original code by Daniel James (danieltj).
Updated for PHP 8.2 and phpBB 3.3.15 compatibility.

## Licence

GPL v2
