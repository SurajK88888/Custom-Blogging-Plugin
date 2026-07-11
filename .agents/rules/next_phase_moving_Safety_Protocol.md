---
trigger: always_on
---

# Phase Transition & Regression Safety Protocol

## Core Directive
Whenever the user announces the start of a new phase, a major feature addition, or a structural pivot, you must halt all code generation and execute the following regression-safety routine before modifying files.

## Mandatory Step-by-Step Routine
1. **Source of Truth Check**: Re-read `.agents/memory/long_term.md` to review the current system architecture boundaries, schema shapes, environment configurations, and technical dependencies.
2. **State Synchronization**: Re-read `.agents/memory/short_term.md` to ensure all previous sub-tasks are fully checked off.
3. **Impact Analysis**: Analyze the codebase and identify any existing files, utility functions, databases, or API endpoints that might conflict, break, or require refactoring during this new implementation.
4. **User Gatekeeping**: Present the results of your Impact Analysis to the user, highlighting any potential breaking changes or architectural risks.
5. **Checklist Update**: Propose a new, granular step-by-step milestone checklist for the new phase and write it into `.agents/memory/short_term.md`. 

## Absolute Constraint
Do not write, modify, or delete any application code for the new phase until the user explicitly reviews your impact analysis and approves your updated milestone checklist.

##While update or change
Maintain the sync between wordpress plugin , backend and all other UI elements and compounds.

##After phase completed
Always mention what next phase is, if available?

Always update the Readme file.