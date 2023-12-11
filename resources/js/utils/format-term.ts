import { AcademicTerm } from '@/types/academic-term';

export const formatYear = (start: number, end: number) => {
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
  return `${formatYear(start, end)} - ${formatTerm(term)}`;
};
