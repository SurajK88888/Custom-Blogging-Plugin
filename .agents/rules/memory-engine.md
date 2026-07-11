---
trigger: always_on
---

---
description: "Triggers on every major code modification, task completion, or session initialization to manage workspace memory"
globs: "**/*"
alwaysApply: true
---

# Global Memory Engine (LTM & STM)

You are equipped with a dual-tier memory system to maintain continuity across separate chat windows and Composer sessions. You must rigorously check and maintain this state.

## 1. Memory Definitions
- **Short-Term Memory (STM) [`.agents/memory/short_term.md`]:** Holds the immediate session context. Includes: current objective, active files under mutation, blocking bugs, and temporary flags.
- **Long-Term Memory (LTM) [`.agents/memory/long_term.md`]:** Holds permanent project truths. Includes: architectural decisions, solved complex bugs (root cause + fix), tech stack choices, and user coding preferences.

## 2. Reading Memory (Bootstrapping Sessions)
- **CRITICAL:** At the absolute start of any task or when asked a question, quietly read both `.agents/memory/short_term.md` and `.agents/memory/long_term.md` using project search or file reading tools.
- Adapt your coding behavior to match the historical preferences and current state found within those files.

## 3. Writing & Syncing State (Updating Memory)
You must explicitly modify the memory files under the following conditions:

### A. Mid-Session State Sync (STM Updates)
- When a user changes the target goal, or a new sub-problem arises, immediately rewrite `.agents/memory/short_term.md` to keep the active state synced.
- Keep the schema strict:
  ```json
  {
    "current_objective": "String",
    "active_files": ["Array"],
    "known_roadblocks": ["Array"],
    "last_updated": "Timestamp"
  }
  ```

### B. Task Completion / Epiphany (LTM Updates)
- When a complex bug is successfully fixed, or a major feature is completed, **do not just say goodbye**.
- You must append a clean Markdown entry to `.agents/memory/long_term.md`. Format it as:
  - `### [Date/Feature Name]`
  - **Context/Problem:** Brief description of what went wrong or what was built.
  - **Resolution:** The exact architectural decision or code fix implemented.
  - **Rule Expansion:** If this fix applies globally, explicitly state if a separate `.md` rule needs editing.

## 4. Rule Synchronization Guardrail
- If an entry added to `.cursor/memory/long_term.md` directly contradicts or significantly updates an existing rule file in `.agents/rules/`, you must immediately alert the user and ask: *"I am updating the Long-Term Memory. Should I also synchronize this change into your [rule-name].md file?"* If they agree, modify the `.md` file using your file editing capabilities.