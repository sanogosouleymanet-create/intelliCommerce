# TODO: Configure Client Messages Page Like Admin Inbox

## Backend Changes
- [x] Add client-specific message routes in web.php (conversation, send, delete)
- [x] Add methods in ClientController: getConversation, sendMessage, deleteMessage
- [x] Ensure client can only access their own conversations (with admins)

## Frontend Changes
- [x] Update resources/views/clients/messages.blade.php to match admin inbox structure (sidebar, chat area, JS)
- [x] Adapt JS for client routes (e.g., /client/messages/conversation/{id}, /client/messages/send)
- [x] Remove admin-specific features like blocking, compose to groups

## Testing
- [x] Test loading conversations
- [x] Test sending replies
- [x] Test deleting messages
- [x] Ensure no access to other clients' messages
