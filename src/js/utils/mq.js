import breakpoints from './breakpoints.js';

/**
 * Check Window.width more than breakpoint
 *
 * @param {string} breakpoint - Breakpoint string
 * @param {boolean} equal - Test breakpoint equality
 * @returns {boolean} - Return breakpoint status
 */
export const isMoreThan = (breakpoint, equal = true) => {
  const bp = breakpoints[breakpoint];

  // Check if 'breakpoint' is valid
  if (!bp) {
    throw new Error(`'${breakpoint}' is not a valid breakpoint`);
  }

  const width = window.innerWidth || document.documentElement.clientWidth;

  // Check for equality
  if (equal && width >= bp) {
    return true;
  }

  if (width > bp) {
    return true;
  }

  return false;
};

/**
 * Check Window.width less than breakpoint
 *
 * @param {string} breakpoint - Breakpoint string
 * @param {boolean} equal - Test breakpoint equality
 * @return {boolean} - Return breakpoint status
 */
export const isLessThan = (breakpoint, equal = false) => {
  const bp = breakpoints[breakpoint];

  // Check if 'breakpoint' is valid
  if (!bp) {
    throw new Error(`'${breakpoint}' is not a valid breakpoint`);
  }

  const width = window.innerWidth || document.documentElement.clientWidth;

  // Check for equality
  if (equal && width <= bp) {
    return true;
  }

  if (width < bp) {
    return true;
  }

  return false;
};
