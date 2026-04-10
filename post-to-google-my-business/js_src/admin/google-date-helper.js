import { dateI18n } from '@wordpress/date';

function parseGoogleDateTime(dateObj, timeObj) {
    if (!dateObj) return null;

    return new Date(
        Number(dateObj.year),
        Number(dateObj.month) - 1,
        Number(dateObj.day),
        Number(timeObj?.hours ?? 0),
        Number(timeObj?.minutes ?? 0),
        Number(timeObj?.seconds ?? 0)
    );
}

function isSameDay(a, b) {
    return !!a && !!b &&
        a.getFullYear() === b.getFullYear() &&
        a.getMonth() === b.getMonth() &&
        a.getDate() === b.getDate();
}

function isAllDay(schedule) {
    const startTime = schedule?.startTime;
    return !startTime || Object.keys(startTime).length === 0;
}

export function formatGoogleEventDate(data) {
    const schedule = data?.event?.schedule;
    if (!schedule?.startDate) return '';

    const start = parseGoogleDateTime(schedule.startDate, schedule.startTime);
    const end = parseGoogleDateTime(schedule.endDate, schedule.endTime);

    if (!start) return '';

    const startDate = dateI18n('j M', start);


    if (isAllDay(schedule)) {
        if (end && !isSameDay(start, end)) {
            return `${startDate} - ${dateI18n('j M', end)}`;
        }
        return startDate;
    }

    if (end && isSameDay(start, end)) {
        return `${startDate}, ${dateI18n('H:i', start)} - ${dateI18n('H:i', end)}`;
    }

    if (end) {
        return `${startDate} - ${dateI18n('j M', end)}`;
    }

    return startDate;
}