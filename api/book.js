// api/book.js - Vercel Edge Function for booking service
export default async function handler(request, response) {
  if (request.method === 'POST') {
    try {
      const { service, name, phone, address, date, description, paymentMethod } = request.body;

      // Validation
      if (!service || !name || !phone || !address || !date) {
        return response.status(400).json({ 
          success: false, 
          error: 'Missing required fields' 
        });
      }

      // Generate booking ID
      const bookingId = `ORV-${Date.now()}`;
      
      // Create booking object
      const booking = {
        id: bookingId,
        service,
        name,
        phone,
        address,
        date,
        description: description || '',
        paymentMethod,
        amount: 500, // ₹500 default
        status: 'pending',
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      };

      // In production, save to database
      // For now, we'll store in a simple in-memory store
      // In real scenario, use Vercel KV or Firebase
      
      console.log('Booking created:', booking);

      // Return success response
      return response.status(200).json({
        success: true,
        message: 'Booking created successfully',
        booking
      });

    } catch (error) {
      console.error('Booking error:', error);
      return response.status(500).json({
        success: false,
        error: 'Failed to create booking'
      });
    }
  } else if (request.method === 'GET') {
    return response.status(405).json({ error: 'Method not allowed' });
  }
}
