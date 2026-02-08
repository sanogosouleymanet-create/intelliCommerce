# TODO: Prevent Blocked Messages in Message Views

## Tasks
- [x] Edit resources/views/vendeurs/messages.blade.php
  - [x] Modify loadConversation to filter messages if blocked
  - [x] Modify updateMenuOptions to hide reply-area if blocked
  - [x] Modify sendReply to prevent sending if blocked
- [x] Edit resources/views/clients/messages.blade.php
  - [x] Modify loadConversation to filter messages if blocked
  - [x] Modify updateMenuOptions to hide reply-area if blocked
  - [x] Modify sendReply to prevent sending if blocked
- [x] Edit resources/views/admin/inbox.blade.php
  - [x] Modify loadConversation to filter messages if blocked
  - [x] Modify updateMenuOptions to hide reply-area if blocked
  - [x] Modify sendReply to prevent sending if blocked

## Followup
- [x] Test the changes to ensure replies are disabled for blocked conversations (messages sent before blocking remain visible)
