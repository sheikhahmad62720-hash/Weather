export function cityTime(unixTs, offset) {
    return new Date((unixTs + offset) * 1000);
}

const UTC_FORMATTER = { timeZone: 'UTC' };

export function formatTime(unixTs, offset) {
    return new Intl.DateTimeFormat('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        ...UTC_FORMATTER,
    }).format(cityTime(unixTs, offset));
}

export function formatHour(unixTs, offset) {
    return new Intl.DateTimeFormat('en-US', {
        hour: 'numeric',
        hour12: true,
        ...UTC_FORMATTER,
    }).format(cityTime(unixTs, offset));
}

export function formatFullDate(location) {
    const now = cityTime(Math.floor(Date.now() / 1000), location.offset);

    return new Intl.DateTimeFormat('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        ...UTC_FORMATTER,
    }).format(now);
}

export function dayLabel(date, index) {
    const [year, month, day] = date.split('-').map(Number);
    const value = new Date(Date.UTC(year, month - 1, day));

    if (index === 0) {
        return 'Today';
    }

    return new Intl.DateTimeFormat('en-US', { weekday: 'short', timeZone: 'UTC' }).format(value);
}

export function compassFromDegrees(degrees) {
    const directions = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];

    return directions[Math.round(((degrees % 360) + 360) % 360 / 45) % 8];
}