import moment from 'moment';
export const requiredRule = [(v) => !!v || "This field is required"];
export const optionalRequiredRule = (v) => !!v || "This field is required";
export const minLengthRule = (minLength) => [
    (v) =>
        (v && v.length >= minLength) || `Minimum length is ${minLength} characters`,
];
export const confirmPasswordMatchRule = (password) => [
    (v) => !!v || "This field is required",
    (v) => v === password || "Passwords do not match",
];

export const onlyAlphabetsRule = [
    (v) => !v || /^[A-Za-z]+(?:\s[A-Za-z]+)*$/.test(v) || "Only alphabets and single spaces between words are allowed.",
];


export const emailRule = [
    (v) => !v || /.+@.+\..+/.test(v) || "Email must be valid",
];
export const numberOnlyRule = [
    (v) => !!v || "This field is required and digit only",
];
export const validateNumberUptoTwoDecimal = [
    (v) => !v || /^\d*(\.\d{1,2})?$/.test(v) || "Enter valid number upto two decimal point",
];
export const zeroPositiveNumberRule = [
    (v) => !!v || "This field is required",
    (v) => /^\d+$/.test(v) || "Only positive numbers are allowed", // Matches only digits (0 and positive numbers)
    (v) => v >= 0 || "Value must be 0 or a positive number",
];
export const validateMobileNumber = [
    (v) => !v || /^\d{10}$/.test(v) || "Please enter 10 digit mobile number", // Ensures the input is exactly 10 digits
];

export const inputNumberRestrict = (input, maxLength) => {
    const numericValue = input.replace(/\D/g, ''); // Remove non-numeric characters
    return numericValue.length > maxLength ? numericValue.slice(0, maxLength) : numericValue;
};

export const validateDate = (value, fieldName) => {
    if (!value) {
        return { isValid: false, error: `${fieldName} is required.` };
    }

    const today = moment().startOf('day'); // Today's date without time
    const selectedDate = moment(value).startOf('day'); // Selected date without time

    if (!selectedDate.isValid()) {
        return { isValid: false, error: 'Invalid date selected.' };
    } else if (selectedDate.isAfter(today)) {
        return { isValid: false, error: `${fieldName} cannot be in the future.` };
    }

    return { isValid: true, error: null }; // Valid case
};

export const validateAadhaar = [
    (v) => !v || /^\d{12}$/.test(v) || "Aadhaar number must be exactly 12 digits", // Ensures the input is exactly 12 digits
];

export const validatePAN = [
    (v) => !v || /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(v) || "Invalid PAN number", // Only validate if input is not empty
];

export const handleAlphaNumericInput = (input, maxLength) => {
    const cleanedInput = input.replace(/[^a-zA-Z0-9]/g, '');
    return cleanedInput.toUpperCase().slice(0, maxLength);
};

export const handleNumberUptoTwoDecimal = (input) => {
    // Remove any non-digit characters except the decimal point
    let sanitizedInput = input.replace(/[^0-9.]/g, '');

    // Ensure only one decimal point
    const decimalCount = (sanitizedInput.match(/\./g) || []).length;
    if (decimalCount > 1) {
        // Keep only the first decimal point and remove the rest
        sanitizedInput = sanitizedInput.replace(/\.(?=.*\.)/g, '');
    }

    // Ensure at most two digits after the decimal
    const [integerPart, decimalPart] = sanitizedInput.split('.');

    if (decimalPart && decimalPart.length > 2) {
        // Truncate decimal part to 2 digits if it exceeds 2
        return `${integerPart}.${decimalPart.substring(0, 2)}`;
    }

    return sanitizedInput;
};

export const validatePincode = [
    (v) => !v || /^\d{6}$/.test(v) || "Invalid Pincode, it must be exactly 6 digits", // Ensures it is a 6-digit number
];

export const validateGSTIN = [
    (v) => !v || /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/.test(v) || "Invalid GSTIN number", // GSTIN format validation
];

export const validateIFSC = [
    (v) => !v || /^[A-Z]{4}0[A-Z0-9]{6}$/.test(v) || "Invalid IFSC code", // GSTIN format validation
];

export const validateCIN = [
    (v) => !v || /^[LUP][0-9]{2}[PU][0-9]{4}[A-Z0-9]{5}$/.test(v) || "Invalid CIN", // GSTIN format validation
];

export const formatToIndianNumberRupee = (number) => {
    return `₹ ${number ? formatToIndianNumber(number) : 0}`;
};

export const formatToIndianNumber = (number) => {
    if (number === null || number === undefined) return '0';

    let numStr = number.toString();

    let [wholePart, decimalPart] = numStr.split('.');

    let lastThree = wholePart.slice(-3);
    let otherDigits = wholePart.slice(0, -3);

    if (otherDigits !== '') {
        otherDigits = otherDigits.replace(/\B(?=(\d{2})+(?!\d))/g, ',');
    }

    let formattedNumber = otherDigits + (otherDigits ? ',' : '') + lastThree;

    if (decimalPart && parseInt(decimalPart) > 0) {
        formattedNumber += '.' + decimalPart;
    }

    return formattedNumber;
};

// Example Usage
// console.log(formatDate("2025-01-28T00:00:00", "ddd MMM DD YYYY HH:mm:ss")); // Tue Jan 28 2025 00:00:00
// console.log(formatDate("2025-01-28T00:00:00", "MMM DD YYYY HH:mm:ss")); // Jan 28 2025 00:00:00
// console.log(formatDate("2025-01-28T00:00:00", "DD-MM-YYYY HH:mm:ss")); // 28-01-2025 00:00:00
// console.log(formatDate("2025-01-28T00:00:00", "YYYY/MM/DD")); // 2025/01/28
// console.log(formatDate("2025-01-28T00:00:00", "HH:mm:ss")); // 00:00:00
// console.log(formatDate("2025-01-28T00:00:00", "dddd, MMMM DD YYYY")); // Tuesday, January 28 2025
// console.log(formatDate("2025-01-28T00:00:00", "ddd, MMM DD YYYY")); // Tuesday, January 28 2025

function formatDateTime(dateString, format) {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) {
        return "Invalid Date";
    }

    const formatMap = {
        "YYYY": date.getFullYear(),
        "YY": String(date.getFullYear()).slice(-2),
        "MMMM": date.toLocaleString('en-US', { month: 'long' }),
        "MMM": date.toLocaleString('en-US', { month: 'short' }),
        "MM": String(date.getMonth() + 1).padStart(2, '0'),
        "DD": String(date.getDate()).padStart(2, '0'),
        "ddd": date.toLocaleString('en-US', { weekday: 'short' }),
        "dddd": date.toLocaleString('en-US', { weekday: 'long' }),
        "HH": String(date.getHours()).padStart(2, '0'),
        "mm": String(date.getMinutes()).padStart(2, '0'),
        "ss": String(date.getSeconds()).padStart(2, '0')
    };

    return format.replace(/\b(YYYY|YY|MMMM|MMM|MM|DD|ddd|dddd|HH|mm|ss)\b/g, match => formatMap[match]);
}


