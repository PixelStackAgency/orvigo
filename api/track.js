// api/track.js - Vercel Edge Function for tracking bookings
export default async function handler(request, response) {
  if (request.method === 'GET') {
    try {
      const { id, phone } = request.query;

      if (!id || !phone) {
        return response.status(400).json({
          success: false,
          error: 'Missing booking ID or phone'
        });
      }

      // In production, fetch from database
      // For now, return mock data
      const booking = {
        id,
        service: 'AC Service & Repair',
        status: 'confirmed',
        date: new Date().toISOString().split('T')[0],
        amount: 500,
        name: 'Customer',
        phone,
        address: 'Sample Address'
      };

      return response.status(200).json({
        success: true,
        booking
      });

    } catch (error) {
      console.error('Track error:', error);
      return response.status(500).json({
        success: false,
        error: 'Failed to track booking'
      });
    }
  } else {
    return response.status(405).json({ error: 'Method not allowed' });
  }
}
