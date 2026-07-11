# Title: Sync Memory and Compress State

## Description
Trigger this skill via `/sync-memory` to archive short-term session progress into long-term architectural memory, update related workspace rules, and reset the active task tracking file.

## Instructions
1. **Read Current State Files:** Locate and read the active contents of `.agents/memory/short_term.md` and `.agents/memory/long_term.md`.
2. **Analyze Session History:** Review the recent conversation or completed files in the current workspace session to identify critical breakthroughs, fixed bugs, or permanent setup changes.
3. **Format Long-Term Archive:** Formulate a clean, chronological Markdown entry for `.agents/memory/long_term.md` under the heading `## Historical Log & Knowledge Base`. Use this layout:
   - `### [Current Date] - [Feature/Task Name]`
   - **What was accomplished:** Short bulleted list of final implementations.
   - **Discovered Quirks/Fixes:** Nasty bugs resolved, structural workarounds, or specific setup patterns discovered.
4. **Identify Rule Synchronization Needs:** Check if any learned breakthroughs contradict or expand on existing `.md` rules in `.agents/rules/`. 
   - If a rule modification is needed, update that specific `.md` file immediately to keep rules synced with long-term memory.
5. **Reset Short-Term State:** Wipe or reset `.agents/memory/short_term.md` back to an idle initializing state so the next coding session starts with a clean slate:
   ```json
   {
     "current_objective": "Idle - Awaiting next objective",
     "active_files": [],
     "known_roadblocks": [],
     "last_updated": "[Current Date]"
   }
   ```
6. **Confirm to User:** Present a concise summary showing exactly what was moved to Long-Term Memory, which rules (if any) were synchronized, and confirm that Short-Term Memory has been cleared.
