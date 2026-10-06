import React from 'react';

export const BANGLADESH_DISTRICTS = [
  'Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna', 'Barisal', 'Rangpur', 'Mymensingh',
  'Gazipur', 'Narayanganj', 'Cumilla', 'Bogra', 'Jessore', 'Cox\'s Bazar', 'Brahmanbaria',
  'Dinajpur', 'Tangail', 'Faridpur', 'Narsingdi', 'Sirajganj', 'Feni', 'Jamalpur', 'Pabna',
  'Noakhali', 'Kushtia', 'Kishoreganj', 'Habiganj', 'Manikganj', 'Munshiganj', 'Netrokona',
  'Sherpur', 'Bagerhat', 'Chuadanga', 'Jhenaidah', 'Magura', 'Meherpur', 'Narail', 'Satkhira',
  'Bandarban', 'Chandpur', 'Khagrachhari', 'Lakshmipur', 'Rangamati', 'Joypurhat', 'Naogaon',
  'Natore', 'Chapai Nawabganj', 'Gaibandha', 'Kurigram', 'Lalmonirhat', 'Nilphamari', 'Panchagarh',
  'Thakurgaon', 'Barguna', 'Bhola', 'Jhalokati', 'Patuakhali', 'Pirojpur', 'Moulvibazar', 'Sunamganj'
];

interface LocationSelectorProps {
  value: string;
  onChange: (district: string) => void;
  className?: string;
}

export const BangladeshLocationSelector: React.FC<LocationSelectorProps> = ({
  value,
  onChange,
  className = '',
}) => {
  return (
    <select
      value={value}
      onChange={(e) => onChange(e.target.value)}
      className={`w-full px-3 py-2 text-sm border border-[var(--border)] rounded-[var(--radius-md)] bg-white focus:outline-none focus:ring-1 focus:ring-black ${className}`}
      required
    >
      <option value="">Select Delivery District</option>
      {BANGLADESH_DISTRICTS.map((district) => (
        <option key={district} value={district}>
          {district} {district === 'Dhaka' ? '(Inside Dhaka — ৳ 70)' : '(Outside Dhaka — ৳ 130)'}
        </option>
      ))}
    </select>
  );
};
