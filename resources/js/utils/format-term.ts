import { AcademicTerm } from '@/types/academic-term';

export const formatAcademicYear = <Start extends number, End extends number>(
  start: Start,
  end: End,
): `${Start}/${End}` | `${Start} - ${End}` => {
  if (end - start === 1) {
    return `${start}/${end}`;
  }

  return `${start} - ${end}`;
};

export const formatTerm = (term: AcademicTerm['term']) => {
  return Number(term) % 2 === 0 ? 'Genap' : 'Ganjil';
};

export const formatEntireTerm = (
  start: number,
  end: number,
  term: AcademicTerm['term'],
) => {
  return `${formatAcademicYear(start, end)} - ${formatTerm(term)}`;
};
