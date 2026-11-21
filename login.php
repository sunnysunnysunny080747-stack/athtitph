<!DOCTYPE html>
<html lang="en" class="bg-gray-950">
<head>
        <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบล็อกอิน</title>
    <link href="css/style.css" rel="stylesheet">
    <!-- <link href="css/bootstrap.css" rel="stylesheet"> -->
     <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css" integrity="sha512-QeR2VH+lsBE5LSAe1Q5EnTBbe7XTBubt8dG93Y7gidSgdMCr8nVqKcfKAMyN96SV8KDbZVTDXChatu5G2KQGzg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="container mx-auto ">
<div class="min-h-screen grid grid-cols-1 md:grid-cols-2 bg-base-100">
  
  <!-- ฝั่งซ้าย: ฟอร์ม Login -->
  <div class="flex items-center justify-center p-8 sm:p-12 lg:p-16">
    <div class="w-full max-w-md space-y-6">
      <!-- ส่วนหัวข้อ -->
      <div class="text-center md:text-left">
        <h1 class="text-3xl font-bold text-base-content">ยินดีต้อนรับกลับมา</h1>
        <p class="text-sm text-base-content/60 mt-2">กรุณากรอกข้อมูลเพื่อเข้าสู่ระบบ</p>
      </div>

      <!-- ฟอร์มกรอกข้อมูล -->
      <form class="space-y-4">
        <!-- อีเมล -->
        <fieldset class="fieldset">
          <legend class="fieldset-legend">อีเมล</legend>
          <input type="email" class="input input-bordered w-full" placeholder="your@email.com" required />
        </fieldset>

        <!-- รหัสผ่าน -->
        <fieldset class="fieldset">
          <legend class="fieldset-legend">รหัสผ่าน</legend>
          <input type="password" class="input input-bordered w-full" placeholder="••••••••" required />
          <div class="flex justify-end mt-1">
            <a href="#" class="link link-hover text-xs text-primary">ลืมรหัสผ่าน?</a>
          </div>
        </fieldset>

        <!-- ตัวเลือกจำฉันไว้ -->
        <div class="form-control">
          <label class="label cursor-pointer justify-start gap-3">
            <input type="checkbox" class="checkbox checkbox-primary checkbox-sm" />
            <span class="label-text">จดจำฉันไว้ในระบบ</span>
          </label>
        </div>

        <!-- ปุ่มกดเข้าสู่ระบบ -->
        <button type="submit" class="btn btn-primary w-full text-lg">เข้าสู่ระบบ</button>
      </form>

      <!-- เส้นคั่นความสวยงาม -->
      <div class="divider text-xs text-base-content/40">หรือเข้าสู่ระบบด้วย</div>


      <!-- สมัครสมาชิก -->
      <p class="text-center text-sm text-base-content/60">
        ยังไม่มีบัญชีผู้ใช้? <a href="#" class="link link-primary font-semibold">สมัครสมาชิกที่นี่</a>
      </p>
    </div>
  </div>

  <!-- ฝั่งขวา: ภาพพื้นหลัง / ข้อความแบรนด์ (จะซ่อนบนมือถือ และแสดงบนจอ md ขึ้นไป) -->
  <div class="hidden md:flex flex-col items-center justify-center bg-neutral text-neutral-content p-12 relative overflow-hidden">
    
    <!-- ข้อความต้อนรับ -->
    <div class=" text-center bg-gray-700 z-10">
     <img src="https://landinggo.com/assets/img/blog/daisyui-alternatives.png" alt="">
    </div>
  </div>

</div>

    
</body>
</html>