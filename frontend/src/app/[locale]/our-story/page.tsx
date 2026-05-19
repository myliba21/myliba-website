import { Container, SectionWrapper, Card } from "@/components/ui";

const teamMembers = [
  { name: "Dilek Mete", title: "Managing Partner, Cultural Transformation Consultant, Executive Coach, PCC, OKR Coach" },
  { name: "Aysel Eker", title: "Partner, Cultural Transformation Consultant, Executive Coach, PCC, OKR Coach" },
  { name: "Huri Sankur", title: "People and Culture Advisor, Academy Coordinator, OKR Coach" },
];

export default function OurStoryPage() {
  return (
    <>
      <SectionWrapper background="white" className="bg-[linear-gradient(180deg,#fbfcff,#eef3ff)]">
        <Container className="max-w-4xl">
          <h1 className="text-5xl md:text-7xl font-black mb-6 tracking-tight">Our Story</h1>
          <div className="rounded-[2rem] border border-border bg-white p-8 shadow-sm">
            <div className="prose prose-lg max-w-none text-text-secondary">
              <p>
                Myliba was founded with a clear mission: to help organizations create high-performance
                cultures by turning strategy into action. We believe that when people are aligned around
                meaningful goals and empowered to contribute, extraordinary results follow.
              </p>
              <p>
                Our name, Myliba — My Life Balance — reflects our belief that sustainable high performance
                comes from balanced, engaged, and motivated teams. We combine the power of OKR methodology
                with cultural transformation expertise to help organizations achieve their full potential.
              </p>
            </div>
          </div>
        </Container>
      </SectionWrapper>

      <SectionWrapper background="surface">
        <Container>
          <h2 className="text-3xl md:text-5xl font-black text-center mb-12 tracking-tight">Our Team</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {teamMembers.map((member) => (
              <Card key={member.name} className="text-center min-h-72">
                <div className="w-28 h-28 bg-gradient-to-br from-[#101523] to-primary rounded-[2rem] mx-auto mb-5 flex items-center justify-center text-2xl font-black text-white">
                  {member.name.split(" ").map((part) => part[0]).join("")}
                </div>
                <h3 className="text-xl font-black">{member.name}</h3>
                <p className="text-sm text-text-secondary mt-2 leading-6">{member.title}</p>
              </Card>
            ))}
          </div>
        </Container>
      </SectionWrapper>
    </>
  );
}
