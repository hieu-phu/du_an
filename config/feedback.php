<?php

return [
    'escalation_hours' => (int) env('FEEDBACK_ESCALATION_HOURS', 24),
    'auto_escalation_check_interval_minutes' => (int) env('FEEDBACK_AUTO_ESCALATION_CHECK_INTERVAL_MINUTES', 5),
];
