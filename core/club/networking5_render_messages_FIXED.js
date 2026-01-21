/**
 * FIX for Reply Message Rendering Issue
 *
 * PROBLEM: When calling await render_messages(replyMessages, null, 1, "reply"),
 * reply messages were only updating the reply count but not rendering the actual message.
 *
 * ROOT CAUSE: Line 720 in optimized version was skipping reply messages incorrectly.
 * The original logic allows single reply messages to be rendered even in "main" layout.
 *
 * SOLUTION: Fixed the condition to properly handle reply rendering based on layout parameter.
 */

// ============================================================================
// FIXED: Lines 719-736 in the optimized render_messages function
// ============================================================================

// Replace this section in your render_messages function:

// ❌ BEFORE (BUGGY CODE - Lines 719-736)
/*
// Skip rendering replies in main layout if not single message update
if (msg.parent_message_id && layout === "main" && messages.length > 1) {
    // Update parent reply count
    const parentId = Number(msg.parent_message_id);
    const replyCount = msgMetadata.replies.get(parentId) || 0;

    if (replyCount > 0) {
        const $parentMsg = $(`#${loadLayout} .chat-list[data-frm_message_id="${parentId}"]`);
        if ($parentMsg.length) {
            const countText = replyCount > 1 ? `${replyCount} replies` : `${replyCount} reply`;
            $parentMsg.find(`.conversation-reply-count[data-id="${parentId}"]`)
                .removeClass('d-none')
                .attr('data-count', replyCount)
                .text(countText);
        }
    }
    continue;
}
*/

// ✅ AFTER (FIXED CODE)
// Handle reply messages based on layout
if (msg.parent_message_id) {
    if (layout === "main") {
        // In main layout, handle reply messages differently
        if (messages.length === 1) {
            // Single reply message: Update parent count and render in reply container
            const parentId = Number(msg.parent_message_id);
            const replyCount = msgMetadata.replies.get(parentId) || 0;

            if (replyCount > 0) {
                const $parentMsg = $(`#channel-conversation-list .chat-list[data-frm_message_id="${parentId}"]`);
                if ($parentMsg.length) {
                    const countText = replyCount > 1 ? `${replyCount} replies` : `${replyCount} reply`;
                    $parentMsg.find(`.conversation-reply-count[data-id="${parentId}"]`)
                        .removeClass('d-none')
                        .attr('data-count', replyCount)
                        .text(countText);
                }
            }

            // ⚡ KEY FIX: Change target container for single reply message
            loadLayout = "chat-reply-conversation-list";
            $container = $(`#${loadLayout}`);

        } else {
            // Multiple messages with replies: Skip rendering replies, only update counts
            const parentId = Number(msg.parent_message_id);
            const replyCount = msgMetadata.replies.get(parentId) || 0;

            if (replyCount > 0) {
                const $parentMsg = $(`#channel-conversation-list .chat-list[data-frm_message_id="${parentId}"]`);
                if ($parentMsg.length) {
                    const countText = replyCount > 1 ? `${replyCount} replies` : `${replyCount} reply`;
                    $parentMsg.find(`.conversation-reply-count[data-id="${parentId}"]`)
                        .removeClass('d-none')
                        .attr('data-count', replyCount)
                        .text(countText);
                }
            }
            continue; // Skip rendering in main view
        }
    }
    // If layout === "reply", continue normal rendering below
}

// Continue with normal rendering...
// (Add date separator, build message HTML, etc.)

