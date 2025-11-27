// api/confirm-payment.js - Vercel Edge Function for payment confirmation
export default async function handler(request, response) {
  if (request.method === 'POST') {
    try {
      const { bookingId, paymentId, orderId, signature } = request.body;

      if (!bookingId || !paymentId) {
        return response.status(400).json({
          success: false,
          error: 'Missing payment details'
        });
      }

      // In production, verify Razorpay signature
      // For now, accept all payments
      
      console.log('Payment confirmed:', {
        bookingId,
        paymentId,
        status: 'success'
      });

      return response.status(200).json({
        success: true,
        message: 'Payment confirmed',
        bookingId,
        paymentStatus: 'success'
      });

    } catch (error) {
      console.error('Payment confirmation error:', error);
      return response.status(500).json({
        success: false,
        error: 'Failed to confirm payment'
      });
    }
  } else {
    return response.status(405).json({ error: 'Method not allowed' });
  }
}
