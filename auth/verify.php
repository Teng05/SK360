<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Exact text and element positioning requires specific font */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        /* Style for the custom input focus states */
        .code-input:focus {
            outline: 2px solid #D32F2F;
            border-color: #D32F2F;
        }
    </style>
</head>
<body class="bg-[#F2F6FB] flex items-center justify-center min-h-screen">
    
    <div class="w-[740px] text-center p-8 bg-[#F2F6FB] shadow-inner">
        <div class="fixed top-0 left-0 w-full bg-[#303133] h-[30px] flex items-center px-4 text-[#C1C4CC] text-xs">
            Verify Account
        </div>
        
        <div class="h-[100px]"></div>

        <div class="flex justify-center mb-6 drop-shadow-[0_15px_15px_rgba(0,0,0,0.15)]">
            <svg class="w-16 h-16" viewBox="0 0 100 100">
                <path d="M30 20 L70 50 L30 80" fill="none" stroke="#D32F2F" stroke-width="12" stroke-linecap="round"/>
                <path d="M70 20 L30 50 L70 80" fill="none" stroke="#2D60B7" stroke-width="12" stroke-linecap="round"/>
            </svg>
        </div>

        <h1 class="text-3xl font-semibold text-[#D32F2F] mb-3">Verify your Account</h1>

        <p class="text-sm text-[#5D6269] mb-6">Enter the code sent to your registered email or phone</p>

        <div class="inline-flex items-center gap-2 bg-[#FEFAD4] border border-[#E9D9AB] px-3 py-1.5 rounded-md mb-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#8F763F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span class="text-[#8F763F] font-medium text-sm">sk360@gmail.com</span>
        </div>

        <div class="bg-white max-w-[430px] mx-auto p-12 rounded-[24px] shadow-[0_25px_50px_rgba(0,0,0,0.1)] mb-12 border border-white">
            
            <div class="flex items-center gap-2.5 mb-10 text-center justify-center">
                <div class="bg-[#FFE5E5] p-2 rounded-full w-9 h-9 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#EC5252]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-7.618 3.033A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <span class="text-[#1F2937] font-semibold text-lg">Enter 6-Digit Code</span>
            </div>

            <div class="grid grid-cols-6 gap-3 mb-12">
                <input type="text" maxlength="1" class="code-input w-full h-[60px] text-center text-3xl font-bold bg-[#F2F3F5] rounded-xl focus:ring-2 focus:ring-[#D32F2F] outline-none">
                <input type="text" maxlength="1" class="code-input w-full h-[60px] text-center text-3xl font-bold bg-[#F2F3F5] rounded-xl focus:ring-2 focus:ring-[#D32F2F] outline-none">
                <input type="text" maxlength="1" class="code-input w-full h-[60px] text-center text-3xl font-bold bg-[#F2F3F5] rounded-xl focus:ring-2 focus:ring-[#D32F2F] outline-none">
                <input type="text" maxlength="1" class="code-input w-full h-[60px] text-center text-3xl font-bold bg-[#F2F3F5] rounded-xl focus:ring-2 focus:ring-[#D32F2F] outline-none">
                <input type="text" maxlength="1" class="code-input w-full h-[60px] text-center text-3xl font-bold bg-[#F2F3F5] rounded-xl focus:ring-2 focus:ring-[#D32F2F] outline-none">
                <input type="text" maxlength="1" class="code-input w-full h-[60px] text-center text-3xl font-bold bg-[#F2F3F5] rounded-xl focus:ring-2 focus:ring-[#D32F2F] outline-none">
            </div>

            <button class="w-full bg-[#D32F2F] hover:bg-[#B71C1C] text-white font-semibold py-4 rounded-xl text-md transition duration-150 mb-7">
                Verify Account
            </button>

            <div class="text-sm">
                <p class="text-[#898E96] mb-4">Didn't receive the code?</p>
                <button class="flex items-center gap-1.5 text-[#D32F2F] hover:text-[#B71C1C] font-semibold mx-auto transition duration-150">
                    <span>Resend Code</span>
                </button>
            </div>

        </div>
    </div>
</body>
</html>